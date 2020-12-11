<?php
/**
 * Class FluentSiteState|Firesphere\SolrFluent\States\FluentSiteState Set the site state for each indexing group for
 * Fluent translations
 *
 * @package Firesphere\Solr\Fluent
 * @author Simon `Firesphere` Erkelens; Marco `Sheepy` Hermo
 * @copyright Copyright (c) 2018 - now() Firesphere & Sheepy
 */

namespace Firesphere\SolrFluent\States;

use Firesphere\SolrSearch\Interfaces\SiteStateInterface;
use Firesphere\SolrSearch\Queries\BaseQuery;
use Firesphere\SolrSearch\States\SiteState;
use ReflectionException;
use TractorCow\Fluent\Extension\FluentExtension;
use TractorCow\Fluent\Model\Locale;
use TractorCow\Fluent\State\FluentState;

/**
 * Class \Firesphere\SolrFluent\States\FluentSiteState
 *
 * Manage the state of the site to apply the correct locale from Fluent to search
 *
 * @package Firesphere\Solr\Fluent
 */
class FluentSiteState extends SiteState implements SiteStateInterface
{
    /**
     * @var array get/set methods that needs to be called to update the query
     */
    private static $methods = [
        'BoostedFields',
        'Filter',
        'Exclude'
    ];
    /**
     * Does the state apply to this class
     *
     * @param string $class Class to check
     * @return bool
     * @throws ReflectionException
     */
    public function appliesTo($class): bool
    {
        return $this->isEnabled() &&
            SiteState::hasExtension($class, FluentExtension::class) &&
            Locale::getCached()->count();
    }

    /**
     * Is this state applicable to this extension
     *
     * @param string $state State to validate
     * @return bool
     */
    public function stateIsApplicable($state): bool
    {
        $locales = Locale::get()->column('Locale');

        return in_array($state, $locales, true) && count($locales);
    }

    /**
     * Reset the SiteState to it's default state
     * Stub method for readability
     *
     * @param string|null $state Reset to default state
     * @return mixed
     */
    public function setDefaultState($state = null)
    {
        $this->activateState($state);
    }

    /**
     * Return the current state of the site
     *
     * @return string|null
     */
    public function currentState()
    {
        return FluentState::singleton()->getLocale();
    }

    /**
     * Activate a given state. This should only be done if the state is applicable
     *
     * @param string $state Activate the given state
     * @return mixed
     */
    public function activateState($state)
    {
        FluentState::singleton()->setLocale($state);
    }

    /**
     * Update the Solr query to match the current State
     *
     * @param BaseQuery $query Query to update
     */
    public function updateQuery(&$query)
    {
        $locale = FluentState::singleton()->getLocale();
        if ($locale === '' || !$locale) {
            return;
        }

        foreach (self::$methods as $method) {
            $this->updatePart($query, $locale, $method);
        }

        $fields = [];
        foreach ($query->getFields() as $field) {
            $fields[] = $field . '_' . $locale;
        }
        $query->setFields($fields);

        $localisedTerms = [];
        foreach ($query->getTerms() as $term) {
            $localisedTerms = $this->updateTerms($term, $locale, $localisedTerms);
        }
        $query->setTerms($localisedTerms);
    }

    /**
     * Update a part of the query for the get and set methods.
     *
     * @param BaseQuery $query Query that needs updating for the given method
     * @param string $locale Localisation to use
     * @param string $method Get method to call
     */
    protected function updatePart(&$query, string $locale, string $method): void
    {
        $new = [];
        $getMethod = 'get' . $method;
        $setMethod = 'set' . $method;
        foreach ($query->$getMethod() as $filterField => $value) {
            $fieldName = $filterField . '_' . $locale;
            $new[$fieldName] = $value;
        }
        $query->$setMethod($new);
    }

    /**
     * Update the field filters to localised filters
     *
     * @param string|array $term Array of terms
     * @param string $locale Localisation to use
     * @param array $localisedTerms Currently localised terms
     * @return array
     */
    protected function updateTerms($term, string $locale, array $localisedTerms): array
    {
        if (count($term['fields'])) {
            foreach ($term['fields'] as &$termField) {
                $termField .= '_' . $locale;
            }
            unset($termField);
        }
        $localisedTerms[] = $term;

        return $localisedTerms;
    }
}

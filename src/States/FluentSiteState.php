<?php
/**
 * Class FluentSiteState|Firesphere\SolrFluent\States\FluentSiteState Set the site state for each indexing group for
 * Fluent translations
 *
 * @package Firesphere\SolrFluent\States
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
 * @package Firesphere\SolrFluent\States
 */
class FluentSiteState extends SiteState implements SiteStateInterface
{
    /**
     * Does the state apply to this class
     *
     * @param $class
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
     * @param string $state
     * @return bool
     */
    public function stateIsApplicable($state): bool
    {
        $locales = Locale::get()->column('Locale');

        return in_array($state, $locales, true) && count($locales);
    }

    /**
     * Reset the SiteState to it's default state
     *
     * @param string|null $state
     * @return mixed
     */
    public function setDefaultState($state = null)
    {
        FluentState::singleton()->setLocale($state);
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
     * @param string $state
     * @return mixed
     */
    public function activateState($state)
    {
        FluentState::singleton()->setLocale($state);
    }

    /**
     * Update the Solr query to match the current State
     *
     * @param BaseQuery $query
     */
    public function updateQuery(&$query)
    {
        $locale = FluentState::singleton()->getLocale();
        if ($locale === '') {
            return;
        }

        $this->updatePart($query, $locale, 'BoostedFields');
        $this->updatePart($query, $locale, 'Filter');
        $this->updatePart($query, $locale, 'Exclude');

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
     * @param $query
     * @param string $locale
     * @param string $method
     */
    protected function updatePart(&$query, string $locale, string $method): void
    {
        if (!$locale) {
            return;
        }
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
     * @param $term
     * @param string $locale
     * @param array $localisedTerms
     * @return array
     */
    private function updateTerms($term, string $locale, array $localisedTerms): array
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

<?php

namespace Firesphere\SolrFluent\States;

use Firesphere\SolrSearch\Interfaces\SiteStateInterface;
use Firesphere\SolrSearch\Queries\BaseQuery;
use Firesphere\SolrSearch\States\SiteState;
use ReflectionException;
use TractorCow\Fluent\Extension\FluentExtension;
use TractorCow\Fluent\Model\Locale;
use TractorCow\Fluent\State\FluentState;

/**
 * Class FluentSiteState
 *
 * @package Firesphere\SolrFluent
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
    public function currentState(): ?string
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
            if (count($term['fields'])) {
                foreach ($term['fields'] as &$termField) {
                    $termField .= '_' . $locale;
                }
                unset($termField);
            }
            $localisedTerms[] = $term;
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
}

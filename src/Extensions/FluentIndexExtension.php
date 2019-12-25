<?php

namespace Firesphere\SolrFluent\Extensions;

use Firesphere\SolrSearch\Indexes\BaseIndex;
use Firesphere\SolrSearch\Queries\BaseQuery;
use Firesphere\SolrSearch\States\SiteState;
use SilverStripe\Core\Extension;
use Solarium\QueryType\Select\Query\Query;
use TractorCow\Fluent\Model\Locale;
use TractorCow\Fluent\State\FluentState;

/**
 * Support for Fluent translations in the index.
 *
 *
 * @package Firesphere\SolrFluent\Extensions
 * @property BaseIndex|FluentIndexExtension $owner
 */
class FluentIndexExtension extends Extension
{
    /**
     * Add the fluent states
     */
    public function onBeforeInit()
    {
        $locales = Locale::get();
        SiteState::addStates($locales->column('Locale'));
    }

    /**
     * Add the needed language copy fields to Solr
     */
    public function onAfterInit(): void
    {
        $locales = Locale::get();
        /** @var BaseIndex $owner */
        $owner = $this->owner;
        $copyFields = $owner->getCopyFields();
        /** @var Locale $locale */
        foreach ($locales as $locale) {
            foreach ($copyFields as $copyField => $values) {
                $owner->addCopyField($locale->Locale . $copyField, $values);
            }
        }
    }

    /**
     * Set to the correct language to search if needed
     *
     * @param BaseQuery $query
     * @param Query $clientQuery
     */
    public function onBeforeSearch($query, $clientQuery): void
    {
        $locale = FluentState::singleton()->getLocale();
        $defaultField = $clientQuery->getQueryDefaultField() ?: '_text';
        $clientQuery->setQueryDefaultField($locale . $defaultField);
    }
}

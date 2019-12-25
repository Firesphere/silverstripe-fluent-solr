<?php


namespace Firesphere\SolrFluent\Extensions;

use Firesphere\SolrSearch\Factories\DocumentFactory;
use SilverStripe\Core\Extension;
use TractorCow\Fluent\State\FluentState;

/**
 * Class Firesphere\SolrFluent\Extensions\FluentDocumentExtension
 *
 * Update Documents per locale
 *
 * @package Firesphere\SolrFluent\Extensions
 * @property DocumentFactory|FluentDocumentExtension $owner
 */
class FluentDocumentExtension extends Extension
{
    /**
     * Update the Solr field for the value to use the locale name
     *
     * @param array $field
     * @param string $value
     */
    public function onBeforeAddDoc(&$field, &$value): void
    {
        $fluentState = FluentState::singleton();
        $locale = $fluentState->getLocale();
        if ($locale) {
            $field['name'] .= '_' . $locale;
        }
    }
}
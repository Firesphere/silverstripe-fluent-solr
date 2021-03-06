<?php
/**
 * Class FluentDocumentExtension|Firesphere\SolrFluent\Extensions\FluentDocumentExtension Add Fluent filtering
 * to queries
 *
 * @package Firesphere\Solr\Fluent
 * @author Simon `Firesphere` Erkelens; Marco `Sheepy` Hermo
 * @copyright Copyright (c) 2018 - now() Firesphere & Sheepy
 */

namespace Firesphere\SolrFluent\Extensions;

use Firesphere\SolrSearch\Factories\DocumentFactory;
use SilverStripe\Core\Extension;
use TractorCow\Fluent\State\FluentState;

/**
 * Class Firesphere\SolrFluent\Extensions\FluentDocumentExtension
 *
 * Update Documents per locale
 *
 * @package Firesphere\Solr\Fluent
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
        /** @var string $locale */
        $locale = $fluentState->getLocale();
        if ($locale) {
            $field['name'] .= '_' . $locale;
        }
    }
}

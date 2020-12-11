<?php
/**
 * Class FluentSchemaExtension|Firesphere\SolrFluent\Extensions\FluentSchemaExtension Add Fluent schema requirements
 *
 * @package Firesphere\Solr\Fluent
 * @author Simon `Firesphere` Erkelens; Marco `Sheepy` Hermo
 * @copyright Copyright (c) 2018 - now() Firesphere & Sheepy
 */

namespace Firesphere\SolrFluent\Extensions;

use Firesphere\SolrSearch\Factories\SchemaFactory;
use SilverStripe\Core\Extension;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;
use TractorCow\Fluent\Model\Locale;

/**
 * Class \Firesphere\SolrFluent\Extensions\FluentSchemaExtension
 *
 * Update the schema with the appropriate locale fields
 *
 * @package Firesphere\Solr\Fluent
 * @property SchemaFactory|FluentSchemaExtension $owner
 */
class FluentSchemaExtension extends Extension
{
    /**
     * Add the locale fields
     *
     * @param ArrayList|DataList $data ArrayList to which the copy should be pushed
     * @param array $item Item that's going to be altered per locale
     */
    public function onAfterFieldDefinition($data, $item): void
    {
        // A locale can be null, those need to be skipped
        $locales = Locale::get()->exclude(['Locale' => null]);

        foreach ($locales as $locale) {
            $copy = $item;
            $copy['Field'] = sprintf('%s_%s', $item['Field'], $locale->Locale);
            $data->push($copy);
        }
    }
}

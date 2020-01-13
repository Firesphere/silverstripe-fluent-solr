<?php
/**
 * Class FluentSchemaExtension|Firesphere\SolrFluent\Extensions\FluentSchemaExtension Add Fluent schema requirements
 *
 * @package Firesphere\SolrFluent\Extensions
 * @author Simon `Firesphere` Erkelens; Marco `Sheepy` Hermo
 * @copyright Copyright (c) 2018 - now() Firesphere & Sheepy
 */

namespace Firesphere\SolrFluent\Extensions;

use Firesphere\SolrSearch\Factories\SchemaFactory;
use SilverStripe\Core\Extension;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use TractorCow\Fluent\Model\Locale;

/**
 * Class \Firesphere\SolrFluent\Extensions\FluentSchemaExtension
 *
 * Update the schema with the appropriate locale fields
 *
 * @package Firesphere\SolrFluent\Extensions
 * @property SchemaFactory|FluentSchemaExtension $owner
 */
class FluentSchemaExtension extends Extension
{
    /**
     * Add the locale fields
     *
     * @param ArrayList|DataList $data ArrayList to which the copy should be pushed
     * @param DataObject $item Item that's going to be altered per locale
     * @todo This needs to be worked out more properly, it's making too much assumptions
     */
    public function onAfterFieldDefinition($data, $item): void
    {
        $locales = Locale::get();

        foreach ($locales as $locale) {
            $isDest = strpos($item['Destination'], $locale->Locale);
            if (($isDest === 0 || $item['Destination'] === null) && $locale->Locale !== null) {
                $copy = $item;
                $copy['Field'] = sprintf('%s_%s', $item['Field'], $locale->Locale);
                $data->push($copy);
            }
        }
    }
}

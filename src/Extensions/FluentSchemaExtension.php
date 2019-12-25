<?php


namespace Firesphere\SolrFluent\Extensions;

use Firesphere\SolrSearch\Services\SchemaService;
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
 * @property SchemaService|FluentSchemaExtension $owner
 */
class FluentSchemaExtension extends Extension
{

    /**
     * Add the locale fields
     *
     * @param ArrayList|DataList $data
     * @param DataObject $item
     */
    public function onAfterFieldDefinition($data, $item): void
    {
        $locales = Locale::get();

        foreach ($locales as $locale) {
            $isDest = strpos($item['Destination'], $locale->Locale);
            if (($isDest === 0 || $item['Destination'] === null) && $locale->Locale !== null) {
                $copy = $item;
                $copy['Field'] = $item['Field'] . '_' . $locale->Locale;
                $data->push($copy);
            }
        }
    }
}

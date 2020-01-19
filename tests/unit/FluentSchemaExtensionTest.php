<?php


namespace Firesphere\SolrFluent\Tests;


use Firesphere\SolrFluent\Extensions\FluentSchemaExtension;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\ArrayList;
use TractorCow\Fluent\Model\Locale;

class FluentSchemaExtensionTest extends SapphireTest
{
    protected static $fixture_file = '../fixtures/FluentTest.yml';

    public function testOnAfterFieldDefinition()
    {
        $data = ArrayList::create();

        $item = [
            'Field' => 'Test'
        ];

        $extension = new FluentSchemaExtension();

        $extension->onAfterFieldDefinition($data, $item);

        $copy = $data->first();

        $this->assertEquals('Test_en_AU', $copy['Field']);

        $this->assertEquals(Locale::get()->count(), $data->count());
    }
}

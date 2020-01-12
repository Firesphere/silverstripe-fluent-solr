<?php


namespace Firesphere\SolrFluent\Tests;

use Firesphere\SolrFluent\Extensions\FluentDocumentExtension;
use SilverStripe\Dev\SapphireTest;
use TractorCow\Fluent\State\FluentState;

class FluentDocumentExtensionTest extends SapphireTest
{
    public function testOnBeforeAddDoc()
    {
        $state = FluentState::singleton()->setLocale('en_US');

        $extension = new FluentDocumentExtension();
        $field = ['name' => 'Test'];
        $value = 'Test';
        $extension->onBeforeAddDoc($field, $value);

        $this->assertEquals('Test_en_US', $field['name']);
    }
}

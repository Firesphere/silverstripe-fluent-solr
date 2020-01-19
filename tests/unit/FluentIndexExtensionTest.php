<?php


namespace Firesphere\SolrFluent\Tests;


use Firesphere\SolrFluent\Extensions\FluentIndexExtension;
use Firesphere\SolrSearch\Queries\BaseQuery;
use Firesphere\SolrSearch\States\SiteState;
use SilverStripe\Dev\SapphireTest;
use Solarium\QueryType\Select\Query\Query;
use TractorCow\Fluent\Model\Locale;
use TractorCow\Fluent\State\FluentState;

class FluentIndexExtensionTest extends SapphireTest
{
    protected static $fixture_file = '../fixtures/FluentTest.yml';

    public function testOnBeforeInit()
    {
        $extension = new FluentIndexExtension();

        $extension->onBeforeInit();

        // Add 2 for default values for the extensions
        $this->assertCount((Locale::get()->count() + 2), SiteState::getStates());
    }

    public function testOnBeforeSearch()
    {
        FluentState::singleton()->setLocale('en_NZ');
        $query = new BaseQuery();
        $clientQuery = new Query();
        $index = new \CircleCITestIndex();

        $extension = new FluentIndexExtension();

        $extension->setOwner($index);

        $extension->onBeforeSearch($query, $clientQuery);

        $field = $clientQuery->getQueryDefaultField();

        $this->assertEquals('en_NZ_text', $field);
    }
}

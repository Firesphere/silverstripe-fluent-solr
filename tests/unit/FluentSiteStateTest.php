<?php


namespace Firesphere\SolrFluent\Tests;

use Firesphere\SolrFluent\States\FluentSiteState;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Dev\SapphireTest;

class FluentSiteStateTest extends SapphireTest
{
    public function testAppliesTo()
    {
        $state = new FluentSiteState();

        $this->assertFalse($state->appliesTo(SiteTree::class));
    }
}

<?php


namespace Firesphere\SolrFluent\Tests;

use Firesphere\SolrFluent\States\FluentSiteState;
use Firesphere\SolrSearch\Queries\BaseQuery;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Dev\SapphireTest;
use TractorCow\Fluent\State\FluentState;

class FluentSiteStateTest extends SapphireTest
{
    public function testAppliesTo()
    {
        $state = new FluentSiteState();

        $this->assertFalse($state->appliesTo(SiteTree::class));
    }

    public function testIsApplicable()
    {
        $state = new FluentSiteState();

        $this->assertFalse($state->stateIsApplicable('en_US'));
    }

    public function testCurrentState()
    {
        $state = new FluentSiteState();

        $this->assertNull($state->activateState('en_US'));

        $this->assertEquals('en_US', $state->currentState());
    }

    public function testDefaultState()
    {
        $state = new FluentSiteState();

        $this->assertNull($state->setDefaultState('en_US'));
    }

    public function testUpdateQuery()
    {
        $query = new BaseQuery();
        FluentState::singleton()->setLocale('');
        $query->addTerm('Test');
        $terms = $query->getTerms();
        $query->addFilter('TestField', 'Value');
        $state = new FluentSiteState();
        $this->assertNull($state->updateQuery($query));

        $this->assertEquals($terms, $query->getTerms());

        FluentState::singleton()->setLocale('en_NZ');

        $query->addTerm('Test2', ['TestField']);
        $query->addField('MyField');
        $state->updateQuery($query);

        $filters = $query->getFilter();
        $fields = $query->getFields();
        $terms = $query->getTerms();

        $this->assertArrayHasKey('TestField_en_NZ', $filters);

        $this->assertEquals('MyField_en_NZ', $fields[0]);

        $this->assertEquals(['TestField_en_NZ'], $terms[1]['Fields']);
    }
}

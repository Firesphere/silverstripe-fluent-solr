<?php

namespace Firesphere\SolrSearch\Fluent;

use Firesphere\SolrSearch\Interfaces\SiteStateInterface;
use Firesphere\SolrSearch\States\SiteState;
use ReflectionException;
use TractorCow\Fluent\Extension\FluentExtension;
use TractorCow\Fluent\Model\Locale;
use TractorCow\Fluent\State\FluentState;

/**
 * Class FluentSiteState
 *
 * @package Firesphere\SolrSearch\Fluent
 */
class FluentSiteState extends SiteState implements SiteStateInterface
{
    /**
     * Does the state apply to this class
     *
     * @param $class
     * @return bool
     * @throws ReflectionException
     */
    public function appliesTo($class): bool
    {
        return $this->isEnabled() &&
            SiteState::hasExtension($class, FluentExtension::class, true) &&
            Locale::getCached()->count();
    }

    /**
     * Is this state applicable to this extension
     *
     * @param string $state
     * @return bool
     */
    public function stateIsApplicable($state): bool
    {
        $locales = Locale::get()->column('Locale');

        return in_array($state, $locales, true);
    }

    /**
     * Reset the SiteState to it's default state
     *
     * @return mixed
     */
    public function setDefaultState()
    {
        /** @var Locale $default */
        $default = Locale::get()->filter(['IsGlobalDefault' => true])->first();

        FluentState::singleton()->activateState($default->Locale);
    }

    /**
     * Return the current state of the site
     *
     * @return string
     */
    public function currentState(): string
    {
        return FluentState::singleton()->getLocale();
    }

    /**
     * Activate a given state. This should only be done if the state is applicable
     *
     * @param string $state
     * @return mixed
     */
    public function activateState($state)
    {
        FluentState::singleton()->activateState($state);
    }
}

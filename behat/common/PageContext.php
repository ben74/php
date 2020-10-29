<?php

use Behat\MinkExtension\Context\MinkContext;

/**
 * Class PageContext
 *
 * @author bfontaine
 */
class PageContext extends MinkContext
{
    use MagentoTrait, ClickTrait, ConfigTrait, DeviceTrait, VisibilityTrait, CustomerAccountTrait;

    /**
     * @param string $configFilePath
     */
    public function __construct($configFilePath)
    {
        $this->setConfigFilePath($configFilePath);
    }

    /**
     * @return string
     */
    public function getConfigPrefixPath()
    {
        return  $this->getDevice() . "/page_elements/";
    }

    /**
     * @When I enter a search string
     */
    public function iEnterASearchString()
    {
        $path = $this->getConfigPrefixPath() . "search_input";
        $selector = $this->getCssSelector($path);
        $el = $this->getSession()->getPage()->find("css", $selector);
        $el->setValue("a");
    }

    /**
     * @When /^I click the ([^"]*)$/
     */
    public function iClickPageElement($element)
    {
        $element = strtolower(str_replace(" ", "_", $element));
        $path = $this->getConfigPrefixPath() . $element;
        $this->clickElement($this->getCssSelector($path));
        $this->iWaitSeconds(1);
    }

    /**
     * @When I follow the main account link
     */
    public function iFollowTheMainAccountLink()
    {
        $path = $this->getConfigPrefixPath() . "account_main_link";
        return $this->clickElement($this->getCssSelector($path));
    }

    /**
     * @Then I be on the account page
     */
    public function iBeOnTheAccountPage()
    {
        return $this->assertSession()->addressEquals("/customer/account/login/");
    }

    /**
     * @Then I should be on the search result page
     */
    public function iShouldBeOnTheSearchResultPage()
    {
        return $this->assertSession()->addressEquals("/catalogsearch/result/?q=");
    }
}

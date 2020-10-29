<?php

/**
 * Class CustomerAccountTrait
 * Magento
 * @author benjamin fontaine
 */
trait CustomerAccountTrait
{
    /**
     * @When I am on the login page
     */
    public function iAmOnTheLoginPage()
    {
        $this->getSession()->visit(Mage::getUrl('customer/account/login'));
    }

    /**
     * @When I login in as a customer
     */
    public function iLoginInAsACustomer()
    {
        $page = $this
            ->getSession()
            ->getPage();
        $page
            ->fillField(
                $this->getCssSelector($this->getConfigPrefixPath() . 'login/username'),
                $this->getCssSelector('customer/account/login')
            )
        ;
        $page
            ->fillField(
                $this->getCssSelector($this->getConfigPrefixPath() . 'login/password'),
                $this->getCssSelector('customer/account/password')
            )
        ;
        $this->clickElement('#send2');
    }

    /**
     * @Then I should be on the account dashboard page
     */
    public function iShouldBeOnTheAccountDashboardPage()
    {
        $this->assertSession()->addressMatches('#.+?customer\/account.+?#');
    }

    /**
     * @Then I should see the login success message
     */
    public function iShouldSeeTheLoginSuccessMessage()
    {
        return $this->assertSession()->pageTextContains($this->getCssSelector($this->getConfigPrefixPath() . 'login/success_message'));
    }

    /**
     * @When I am on the create account page
     */
    public function iAmOnTheCreateAccountPage()
    {
        $this->getSession()->visit(Mage::getUrl('customer/account/create'));
    }

    /**
     * @When I create an account
     */
    public function iCreateAnAccount()
    {
        $this->getSession()->getPage()->find('css', 'button')->click();
    }

    /**
     * @When I am logged in a customer
     */
    public function iAmLoggedInACustomer()
    {
        $this->iAmOnTheLoginPage();
        $this->iLoginInAsACustomer();
    }

    /**
     * @When I go to the address book page
     */
    public function iGoToTheAddressBookPage()
    {
        $this->getSession()->visit(Mage::getUrl('customer/address/new'));
    }

    /**
     * @When I save my address
     */
    public function iSaveMyAddress()
    {
        $this->getSession()->getPage()->find("css", 'button')->click();
    }

    /**
     * @Then the address should be saved
     */
    public function theAddressShouldBeSaved()
    {
        $this->assertSession()->addressMatches('#.+?customer\/address\/index.+?#');
    }

    /**
     * @When I go to the orders page
     */
    public function iGoToTheOrdersPage()
    {
        $this->getSession()->visit(Mage::getUrl('sales/order/history'));
    }

    /**
     * @Then I should see my order history
     */
    public function iShouldSeeMyOrderHistory()
    {
        return $this->assertSession()->pageTextContains($this->getCssSelector($this->getConfigPrefixPath() . 'login/order_history'));
    }

    /**
     * @When I go to the personnal information page
     */
    public function iGoToThePersonnalInformationPage()
    {
        $this->getSession()->visit(Mage::getUrl('customer/account/edit/'));
    }

    /**
     * @Then I should see the personnal information
     */
    public function iShouldSeeThePersonnalInformation()
    {
        return $this->assertSession()->pageTextContains($this->getCssSelector($this->getConfigPrefixPath() . 'login/personnal_information'));
    }
}

<?php

use \Behat\Mink\Exception\ExpectationException as ExpectationException;

#require_once("../../app/Mage.php");

/**
 * Class MagentoTrait
 *
 * @author benjamin fontaine
 */
trait MagentoTrait
{
    /**
     * @BeforeSuite
     * anything running before all tests
     */
    public static function prepareBeforeSuite()
    {
        #Mage::app();
    }

    /**
     * @Given I wait :arg1 seconds
     */
    public function iWaitSeconds($arg1)
    {
        sleep($arg1);
    }

    /**
     * Visit a product page by SKU
     *
     * @When I visit the product "([^"]*)"$/
     */
    public function iVisitProductPagebySku($sku)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel("catalog/product")->loadByAttribute("sku", $sku);
        if ($product) {
            $this->getSession()->visit($product->getProductUrl());
        } else {
            throw new ExpectationException('Product "%s" either does not exist or is not visible', $sku);
        }
    }
}

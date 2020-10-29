<?php
#uses EcomDev_PHPUnit_Model_App
trait FixturesTrait
{
    private $hasfixtures = false, $discarded = false;
    /**
    replace the wished value from basic fixture template
     * @When load derivated fixture :base :sku :inc
     * @When load derivated fixture :base :sku
     * @When load derivated fixture :base
     * @When load derivated fixture
     */
    public function loadDerivatedFixture($base = 'fixtures/1productInStock.yaml', $sku='aFixtureItem' ,$inc = 1)
    {
        $x = file_get_contents(__DIR__ . '/' . $base);
        $y = [
            'INC'         => $inc,
            'GID'         => 1,#group_id:1
            'CATEGORYID'  => 1,
            'SKU'         => $sku,
            'STOREID'     => $_SESSION['storeid'],
            'STORECODE'   => $_SESSION['storecode'],
            'COUNTRYISO'  => $_SESSION['countryiso'],
            'WEBSITEID'   => $_SESSION['website_id'],
            'WEBSITECODE' => $_SESSION['website_code'],#sln_fr_fr
        ];
        foreach ($y as $k => $v) {
            $x = str_replace('##' . $k . '##', $v, $x);
        }
#trim all comments
        $x=trim(preg_replace("~^ *#[^\n]+\n~m",'',$x));

        file_put_contents(__DIR__ . '/fixtures/default.yaml', $x);
        $this->loadFixtureFromFile(__DIR__ . '/fixtures/default.yaml', $base);
    }

    /**
     * loads specified fixture file
     * @string $file
     *
     * @When load fixture from file :file
     * ex : when load fixture from /var/www/html/emea2/tests/functional/app/code/local/Amer/Flow/Model/Idoc/fixtures/test_getIdocs_givenManufacturedOrder_shouldReturnCorrectArrayToSendToSap.yaml
     * Only run them once --> mage::registry ??
     */
    public function loadFixtureFromFile($file, $original = null)
    {
        if (!$this->hasfixtures) {
            $this->hasfixtures = true;
            register_shutdown_function([$this, 'discardFixtures']);
            echo "fixtures : should only init once";

            $this->storage=new Varien_Object();
            Mage::register(EcomDev_PHPUnit_Model_App::REGISTRY_PATH_SHARED_STORAGE, $this->storage);
            Mage::getSingleton('EcomDev_PHPUnit_Model_App')::applyTestScope();
            $this->f = Mage::getSingleton('ecomdev_phpunit/fixture');
            $this->f->setStorage($this->storage)->setScope('shared');
        }
        if (!is_file($file)) {
            $this->assertSession()->pageTextContains("#! is not a file : $file\n");
        }
        $this->gt(1);
        $this->f->loadYaml($file)->apply();
        print " - loaded in " . $this->gt() . " ms";
    }

    /**
     * @loadFixtures
     * @loadFixture
     * only perform it once, otherwise we can change this key to run multiple fixtures, then a single discard shall
     * do the trick.. default.yaml
     */
    public function loadFixtures()
    {
        if (Mage::registry(EcomDev_PHPUnit_Model_App::REGISTRY_PATH_SHARED_STORAGE)) {
            return;
        }
        if (!$this->hasfixtures) {
            $this->hasfixtures = true;
            register_shutdown_function([$this, 'removeFixtures']);
        }

        $this->gt(1);
        $file = get_class($this);

        Mage::register(EcomDev_PHPUnit_Model_App::REGISTRY_PATH_SHARED_STORAGE, new Varien_Object());

        $f = EcomDev_PHPUnit_Test_Case_Util::getFixture($file)
            ->setScope(EcomDev_PHPUnit_Model_FixtureInterface::SCOPE_SHARED)
            ->loadForClass($file);

        $annotations = PHPUnit_Util_Test::parseTestMethodAnnotations($file);

        $f->setOptions($annotations['class'])->apply();
        print "fixtures in " . $this->gt() . " ms\n";
    }

    /**
     * @removeFixtures
     * remove fixtures per file
     */
    public function removeFixtures()
    {
        if ($this->discarded) {
            return;
        }
        $this->gt(1);
        $this->discarded = true;
        EcomDev_PHPUnit_Test_Case_Util::getFixture(get_class($this))->discard();
        print "\n\n\t\tdiscarded in " . $this->gt() . " ms\n";
    }


    /**
     * discards all fixtures, only perform once
     */
    public function discardFixtures()
    {
        if ($this->discarded) {
            return;
        }
        $this->gt(1);
        $this->discarded = true;
        $f = Mage::getSingleton('ecomdev_phpunit/fixture');
        $f->discard();
        print "\n\n\t\tdiscarded in " . $this->gt() . " ms\n";
    }

}

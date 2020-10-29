<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

#@BeforeScenario -> before each scenario
#@AfterScenario -> after each scenario
/**
 * Class FeatureContext
 * Defines application features from the specific context.
 *
 * @author bf
 * @loadFixture
 * @loadSharedFixture
 */
class FeatureContext extends PageContext implements Context
{
    use FunctionsTrait;
    use FixturesTrait;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     *
     * @param string $configFilePath
     * @param string $urls
     * @param array  $user
     */
    public function __construct(string $configFilePath, $urls = [], $user = [])
    {
        $config = [];
        $config=$this->parseOptions();
        if ($config['lang'] && is_file($config['lang'])) {
            $x = file_get_contents($config['lang']);
            $y = json_decode($x, 1);
            $_ENV['conf'] = $y;
            if($y['selenium']){
/** do the special stuff here */
            }
        }
        $this->setConfigFilePath($configFilePath);
    }

    /**
     * @When I Fill The Paypal Credentials
     * @When I check there are paypal credentials :checkonly
     * todo : put them in a yaml config file
     */
    public function iFillThePaypalCredentials($checkonly=0)
    {
        if (!isset($credentials[$_SESSION['lang']])) {
            die("\nno paypal credentials for : " . $_SESSION['lang'] . "\n");
        }
        if ($checkonly) {
            return;
        }

        list($login, $pass) = explode(',', $credentials[$_SESSION['lang']]);

        $this->evaljs(
            " (x=document.injectedUl) && x.$('#email').val('" . $login . "').length && x.$('#password').val('" . $pass
            . "').length && x.$('#btnLogin').css('background','#F00').click().length", 15000, 1000
        );
    }

    /**
     * @When I abort
     * @When I abort the mission
     * @When abort
     * @When exit
     * @When dies
     */
    public function iAbortTheTest()
    {
        die(__FILE__ . ':' . __LINE__ . ":: aborted\n\n");
    }

    /**
     * @When wait
     * @When iwait
     * @When wait :to
     * @When iwait :to
     * i wait n miliseconds
     */
    public function iwait($ms = 300)
    {
        usleep($ms * 1000);
    }

    /**
     * @When isjqready
     * @When isjqready :time
     * @When ieval :condition :time :msStep
     * @When ieval :condition :time
     * @When ieval :condition
     * @When until :condition
     *
     * @param string  $condition
     * @param integer $time
     * @param integer $msStep
     * @param integer $verbose

     * evaluates a javascript expression with a timeout, beware to carefully write an expression which fails befor
     */
    public function ieval($condition = 1, $time = 60000, $msStep = 600, $verbose = 1 )
    {
        /** performs the check that jquery is ready without any pending ajax requests - registerting a little alias */
        $this->evaljs("( document && typeof jQuery == 'function' && jQuery.active === 0)", $time, $msStep, 0);
        # && (j=jQuery.noConflict())

        /** enough for now =) :: simple jQuery && no pending ajax test */
        if ($condition === 1) {
            return 1;
        }

        return $this->evaljs($condition, $time, $msStep, $verbose);
    }

    /**
     * @When fails :condition
     * @When failjs :condition
     * if returns true, then fails
     */
    public function failjs($condition)
    {
        $res = $this->getSession()->evaluateScript($condition);
        if ($res) {
            $this->assertSession()->pageTextContains('#! fails ');
        }
    }

    /**
     * @When evaljs :condition :time :msStep
     * @When evaljs :condition :time
     * @When evaljs :condition
     */
    public function evaljs($condition, $time = 60000, $msStep = 1000, $verbose = 1)
    {
        $res = null;
        $start = $this->gt(1);
        $end = $start + $time;
        $now = 1;

        while (!$res && $now < $time) {
            $res = $this->getSession()->evaluateScript($condition);
            if (!$res) {
                if ($verbose) {
                    if ($verbose < 2) {
                        $verbose++;
                        ob_end_flush();
                    }
                    print '.';
                }
                usleep($msStep * 1000);
            }
            $now = $this->gt();
        }

        if ($res) {
            if ($verbose) {
                print ' => ok in ' . $this->gt() . ' ms';
            }
            return 1;
        }
        if(0){
        /*** printing the condition which fails could be really usefull !!! */
        $condition = trim($condition, ' ()&;');
        $conditions = explode('&&', $condition);
        foreach ($conditions as $cond) {
            $res = $this->getSession()->evaluateScript($cond);
            if (!$res) {
                $res = $cond;
                $conditions = [];
            }
        }
        }
        $this->assertSession()->pageTextContains('#! '.$res.' fails in ' . $this->gt() . '/' . $time . ' ms');
        return 0;
    }


    /**
     * @When isready :a2 :a1
     * @When isready :a2
     * @When isready
     * @When ready
     * waits n miliseconds for jquery to be loaded and no more pending ajax
     */
    public function isready($a2 = 25000, $a1 = null)
    {
        $res = $this->ieval(1, $a2);
        if ($res && $a1) {
            $this->getSession()->evaluateScript($a1);
        }
    }

    /**
     * @Then I am on the home page
     * @Then is home page
     */
    public function isHomePage()
    {
        $this->assertSession()->addressEquals($_SESSION['baseurl']);
    }

    /**
     * @Then page adress is like :url
     * @Then page like :url
     * @Then url like :url
     * behat detect if page has changed and charged
     */
    public function pageLike($url)
    {
        $this->assertSession()->addressMatches('~' . $url . '~i');
        return;
        $this->JS(
            "changed=0;jQuery(window).blur(function(){changed=1;});jQuery(window).focus(function(){changed=2;});", $a2
        );
        $res = $this->ieval("(changed)");
    }

    /**
     * @When ajaxok
     * @When ajaxok :a2
     * @When ajax requests are ok :a2
     * waits n miliseconds for the last ajax request to succeed
     */
    public function ajaxok($a2 = 45000)
    {
        $this->ieval(1);
    }

    /**
     * @When addJQ
     * add jquery to the current document if required
     */
    public function addJQ()
    {
        #if typeof jQuery !== 'function' && not within document.script src tags
        $this->getSession()->evaluateScript(
            "function ad(x){s=d.createElement('script');s.type='text/javascript';s.async=1;s.src=x;d.head.appendChild(s);}d=document;ad('https://code.jquery.com/jquery-1.12.4.min.js');",
            $a2
        );
    }

    /**
     * @When JS :js
     * @When I run js :js
     */
    public function JS($js)
    {
        $this->wait(1,$js);
    }

    /**
     * @When I go on simulcms page
     */
    public function iGoOnSimulcmsPage()
    {
        $url = $this->relativeUrl('/simul_cms/' . $_SESSION['countrycode']);
        $this->go($url);
    }

    /**
     * @When go :url
     */
    public function go($url)
    {
        ob_end_flush();
        echo '=> ' . $url;
        $this->getSession()->visit($url);
        echo '.';
    }

    /**
     * @Then Page :a1 contains :a2
     */
    public function pageContains($a1, $a2)
    {
        $url = $this->relativeUrl($a1);
        echo '=> ' . $url;
        $this->getSession()->visit($url);
        return $this->assertSession()->pageTextContains($a2);
    }

    /**
     * @When fails
     * @When it fails
     */
    public function fails()
    {
        $this->assertSession()->pageTextContains('###fails###');
    }

    /**
     * @Then contains :a1
     */
    public function contains($a1)
    {
        return $this->assertSession()->pageTextContains($a1);
    }

    /**
     * @Then fails if contains :a1
     */
    public function failsIfContains($a1)
    {
        return $this->assertSession()->pageTextNotContains($a1);
    }

    /**
     * Magento thing uses : Mage::app()
     * @Given getStoreUrlPerStoreCode
     */
    public function getStoreUrlPerStoreCode()
    {
        @session_start();
        if ($_SESSION['baseurl']) {
            return;
        }

        $options = $this->parseOptions();
        $storeId = $options['lang'];

        $storesIds = array_keys(Mage::app()->getStores());
        asort($storesIds);
        array_shift($storesIds);

        if (!is_numeric($storeId)) {
            $storeId = (int)reset($storesIds);
            echo "using first declared store id =>" . $storeId."\n";
        }

        if (!in_array($storeId, $storesIds)) {
            die("#error#store_id not within the list : " . $options['lang'] . ' not in : ' . implode(
                    ',', $storesIds
                ));
        }

        $x = Mage::getModel('core/store')->load($storeId);
        if (!$x) {
            die("#error#store_id not found for : " . $storeId);
        }
#??? pourquoi peut il être vide ???? => car fixturé /..
        $website = $x->getWebsite();
        if (!$website) {
            die("#error#no website .. \n");
        }

        $storeCode = $x->getCode();#suu_fr_fr
        $lang = $countryCode = strtolower(Mage::getStoreConfig('general/country/allow', $storeId));#fr

        /*countrycodes are sometimes .. */
        if (!$countryCode or strlen($countryCode) > 8) {
            die("#error#countrycode : " . $countryCode);
        }

        if ($x->getUrlKey()) {
            $lang = $x->getUrlKey();
        }#fr, seulement sur 
        $wsname = $website->getName();
        if (stripos($wsname, 'WEBSITENAME') !== false) {
            $countryCode = $this->Magento_Locales($wsname, $lang);#convert
        }
        /** get the right simulcms bindings !! */
        if (!$countryCode or strlen($countryCode) > 6 or is_numeric($countryCode)) {
            die("#error#countrycode : " . $countryCode);
        }

        $url = Mage::getStoreConfig('web/unsecure/base_link_url', $storeId);
        $baseHost = $this->basehost($url);#
        $baseUrl = $baseHost . '/' . $lang . '/';
        /** memorise && acts as a failsafe, as we couldn't get mage::registry values on wilson */
        $_SESSION = [
            'lang' => $lang,
            'wsname' => $wsname,
            'baseurl' => $baseUrl,
            'storeId' => $storeId,
            'basehost' => $baseHost,
            'store_code' => $storeCode,
            'countrycode'  => $countryCode,
            'website_id' => $x->getWebsite()->getId(),
            'website_code' => $x->getWebsite()->getCode(),
            'countryiso'  => strtolower(substr($countryCode,0,2))
        ];
        print_r($_SESSION);
        #foreach ($_SESSION as $k => $v) {Mage::register($k,  $v);}
        #echo $options['lang'] . ' => ' . $baseUrl . '#' . $storeId . '#' . $lang . '#' . $countryCode . '#' . $wsname;
    }

    /**
     * @Given baseurl is the local one
     */
    public function baseurlIsTheLocalOne()
    {
        if (!$_SESSION['baseurl']) {
            $url = Mage::getStoreConfig(Mage_Core_Model_Url::XML_PATH_SECURE_URL);
            $_SESSION['baseurl'] = $url;
            $_SESSION['basehost'] = $this->basehost($url);
        };
        return 1;
    }

    /**
     * @Given baseurl is :url
     */
    public function baseurlIs($url)
    {
        if (!$_SESSION['baseurl']) {
            $_SESSION['baseurl'] = $url;
            $_SESSION['basehost'] = $this->basehost($url);
        };
        return 1;
    }

    /**
     * @Given /^I reset the session$/
     */
    public function iResetTheSession() {
        $this->getSession()->reset();
    }

    /**
     * @When I visit :a1
     */
    public function iVisit($a1)
    {
        $url = $this->relativeUrl($a1);
        $this->go($url);
    }

    /**
     * @When mayday
     */
    public function mayday()
    {
        die('mayday');
        return 1;
    }

    /**
     * @When nothing happens
     */
    public function nothingHappens()
    {
        return 1;
    }

    /**
     * @When I wait for :text to appear
     * @Then I should see :text appear
     *
     * @param $text
     *
     * @throws \Exception
     */
    public function iWaitForTextToAppear($text)
    {
        $this->spin(
            function (FeatureContext $context) use ($text) {
                try {
                    $context->assertPageContainsText($text);
                    return true;
                } catch (ResponseTextException $e) {
                    #nothing becuz spinnin
                }
                return false;
            }
        );
    }

    /**
     * @When I Click On :elementSelector
     * @param $elementSelector
     * When I Click On "jQuery('a')[0]"
     */
    public function iClickOn($elementSelector){
        $this->evaljs("(a=1) && (el=$elementSelector) && (function(){var e=document.createEvent('MouseEvents');e.initMouseEvent('click',true,true,window,0,0,0,0,0,false,false,false,false,0,null);a.dispatchEvent(el);return 1;}())");
    }

}

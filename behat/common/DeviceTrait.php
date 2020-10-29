<?php

/**
 * Class DeviceTrait
 *
 * @author benjamin fontaine
 */
trait DeviceTrait
{
    /**
     * @var string
     */
    protected static $_device = "desktop";

    /**
     * @var array
     */
    protected static $_deviceTags = [
            "desktop",
            "tablet_large",
            "tablet_small",
            "mobile",
        ];

    /**
     * Get Device
     *
     * @return string
     */
    public function getDevice()
    {
        return static::$_device;
    }

    /**
     * Sets the device to be used in the config path to get the CSS selector
     *
     * @BeforeFeature
     */
    public static function prepareBeforeFeature(Behat\Behat\Hook\Scope\BeforeFeatureScope $featureScope)
    {
        $feature = $featureScope->getFeature();
        return self::_setDevice($feature);
    }

    /**
     * Sets the device to be used in the config path to get the CSS selector
     *
     * @BeforeScenario
     */
    public static function prepareBeforeScenario(Behat\Behat\Hook\Scope\BeforeScenarioScope $scenarioScope)
    {
        $scenario = $scenarioScope->getScenario();
        return self::_setDevice($scenario);
    }

    /**
     * Set Device Tags
     *
     * @param array $tags tags
     *
     * @return void
     */
    protected function _setDeviceTags(array $tags)
    {
        self::$_deviceTags = $tags;
    }


    /**
     * Set Device
     *
     * @param stdClass $object object
     *
     * @return bool
     */
    protected static function _setDevice($object)
    {
        foreach (static::$_deviceTags as $device) {
            if ($object->hasTag($device)) {
                static::$_device = $device;
                return true;
            }
        }
        return false;
    }
}

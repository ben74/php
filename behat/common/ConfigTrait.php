<?php

use \Symfony\Component\Yaml\Yaml,
    \Behat\Mink\Exception\ExpectationException as ExpectationException;

/**
 * Class ConfigTrait
 *
 * @author benjamin fontaine
 */
trait ConfigTrait
{
    /**
     * @var string
     */
    protected $_configFilePath;

    /**
     * Set Config File Path
     *
     * @param string $configFilePath config file path
     *
     * @return void
     */
    public function setConfigFilePath($configFilePath)
    {
        $this->_configFilePath = $configFilePath;
    }

    /**
     * Get Config File Path
     *
     * @return string
     */
    public function getConfigFilePath()
    {
        return $this->_configFilePath;
    }


    /**
     * Get Css Selector
     *
     * @param string $path      path
     * @param string $delimiter delimiter
     *
     * @return string
     * @throws ExpectationException
     */
    public function getCssSelector($path, $delimiter = "/")
    {
        $filePath = $this->getConfigFilePath();
        if (!$filePath) {
            throw new ExpectationException(
                "Behat context parameter for configFilePath needs to be set.", $this->getSession()
            );
        }
        $config = $this->getConfig($filePath);
        $paths = explode($delimiter, $path);
        return $this->getValue($paths, $config);
    }

    /**
     * Get Value
     *
     * @param array $args   args
     * @param array $config config
     *
     * @return array|mixed
     * @throws ExpectationException
     */
    public function getValue(array $args, array $config)
    {
        $value = $config;
        foreach ($args as $key) {
            if (!array_key_exists($key, $value)) {
                throw new ExpectationException("Configuration path '$key' was not found", $this->getSession());
            }
            $value = $value[$key];
        }

        return $value;
    }

    /**
     * Get Config
     *
     * @param string $filePath file path
     *
     * @return mixed
     * @throws ExpectationException
     */
    public function getConfig($filePath)
    {
        try {
            $content = file_get_contents($filePath);
        } catch (\Exception $e) {
            throw new ExpectationException($e->getMessage(), $this->getSession());
        }

        try {
            $config = Yaml::parse($content);
        } catch (\Exception $e) {
            echo $e->getMessage();
            throw new ExpectationException("Cannot parse yaml file '$filePath'", $this->getSession());
        }
        return $config;
    }
}

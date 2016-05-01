<?php

namespace R3H6\Error404page\Configuration;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class ExtensionConfiguration implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * @var string
     */
    const EXT_KEY = 'error404page';

    /**
     * @var array
     */
    protected $configuration = array();

    public function __construct()
    {
        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][self::EXT_KEY])) {
            if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][self::EXT_KEY])) {
                $this->configuration = $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][self::EXT_KEY];
            } else {
                $this->configuration = (array) unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][self::EXT_KEY]);
            }
        }
    }

    private function _get($key)
    {
        return isset($this->configuration[$key]) ? $this->configuration[$key]: null;
    }

    public function __call($method, $arguments)
    {
        if (method_exists($this, '_' . $method)) {
            return call_user_func_array([$this, '_' . $method], $arguments);
        }
        throw new \RuntimeException("Method $method doesn't exist", 1461958193);
    }

    public static function __callStatic($method, $arguments)
    {
        $instance = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(self::class);
        return call_user_func_array([$instance, $method], $arguments);
    }
}

<?php

namespace Monogon\Page404\Configuration;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class ExtensionConfiguration implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * @var string
     */
    const EXT_KEY = 'page404';

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

    public function __call($name, $arguments)
    {
        if (isset($this->configuration[$name])) {
            return $this->configuration[$name];
        }
        return null;
    }

    public static function __callStatic($name, $arguments)
    {
        return GeneralUtility::makeInstance(ExtensionConfiguration::class)->$name();
    }
}

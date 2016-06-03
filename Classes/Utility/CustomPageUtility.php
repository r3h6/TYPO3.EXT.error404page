<?php

namespace R3H6\Error404page\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

class CustomPageUtility
{
    private static $concreteClassName = null;

    public static function addDoktype($extKey, $doktype, $iconName)
    {
        call_user_func_array([static::getConcreteClassName(), 'addDoktype'], func_get_args());
    }

    public static function addDoktypeToPages($extKey, $doktype, $iconName, $alias = null)
    {
        call_user_func_array([static::getConcreteClassName(), 'addDoktypeToPages'], func_get_args());
    }

    public static function addDoktypeToPagesLanguageOverlay($extKey, $doktype, $iconName, $alias = null)
    {
        call_user_func_array([static::getConcreteClassName(), 'addDoktypeToPagesLanguageOverlay'], func_get_args());
    }

    private static function getConcreteClassName()
    {
        if (static::$concreteClassName === null) {
            if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '7.6.0', '>=')) {
                static::$concreteClassName = CustomPageUtilityV7::class;
            } else {
                static::$concreteClassName = CustomPageUtilityV6::class;
            }
        }
        return static::$concreteClassName;
    }
}

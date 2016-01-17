<?php

namespace TYPO3\CMS\Core\Utility;

class GeneralUtility
{
    public static function __callStatic($name, $arguments)
    {
        echo "***$name";
    }
}

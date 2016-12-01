<?php

namespace R3H6\Error404page\Tests\Unit;

/*                                                                        *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 3 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        */

// require_once __DIR__ . '/../Fixtures/Request.php';

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * UnitTestCase.
 */
class UnitTestCase extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    public function setUpTYPO3Globals()
    {
        $GLOBALS['TYPO3_DB'] = $this->getMock('TYPO3\\CMS\\Core\\Database\\DatabaseConnection', get_class_methods('TYPO3\\CMS\\Core\\Database\\DatabaseConnection'), array(), '', false);

        $configurationManager = $this->getMock('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager', get_class_methods('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager'), array(), '', false);
        GeneralUtility::setSingletonInstance('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager', $configurationManager);

        $GLOBALS['TSFE'] = new \stdClass();
        $GLOBALS['TSFE']->sys_language_uid = 0;
        $GLOBALS['TSFE']->csConvObj = $this->getMock('TYPO3\\CMS\\Core\\Charset\\CharsetConverter', get_class_methods('TYPO3\\CMS\\Core\\Charset\\CharsetConverter'), array(), '', false);
    }
    public function tearDownTYPO3Globals()
    {
        unset($GLOBALS['TYPO3_DB'], $GLOBALS['TSFE']);
    }
}

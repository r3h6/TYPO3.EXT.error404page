<?php

namespace R3H6\Error404page\Tests\Unit\Utility;

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

use R3H6\Error404page\Utility\RegexUtility;

/**
 * Test case for class \R3H6\Error404page\Utility\RegexUtility.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @author R3 H6 <r3h6@outlook.com>
 */
class RegexUtilityTest extends \Nimut\TestingFramework\TestCase\UnitTestCase
{
    use \R3H6\Error404page\Tests\TestCaseCompatibility;
    use \R3H6\Error404page\Tests\DeprecationLogTrait;

    /**
     * @test
     * @dataProvider isValidDataProvider
     */
    public function isValid($expected, $regex)
    {
        $this->assertSame($expected, RegexUtility::isValid($regex));
    }

    public function isValidDataProvider()
    {
        return array(
            array(true, '/test/'),
            array(false, '/test\/'),
            array(false, '/test\\/'),
            array(true, '/test\\\/'),
            array(false, '/test/e/i'),
        );
    }
}

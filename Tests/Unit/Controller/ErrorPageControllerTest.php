<?php

namespace Monogon\Page404\Tests\Unit\Controller;

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

// require_once __DIR__ . '/../Fixtures/GeneralUtility.php';

use Monogon\Page404\Controller\ErrorPageController;

/**
 * Unit test for the ErrorPageController.
 */
class ErrorPageControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    protected $subject;

    protected $params = [
        'reasonText' => 'Unit test',
        'currentUrl' => '/invalid/path/',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMock(ErrorPageController::class, [], [], '', false);
        $GLOBALS['TSFE'] = $this->getMock(\TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController::class, [], [], '', false);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->subject, $GLOBALS['TSFE']);
    }

    /**
     * @test
     */
    public function handleError()
    {

        $this->subject->handleError($this->params, $GLOBALS['TSFE']);
    }
}

<?php

namespace R3H6\Error404page\Tests\Unit\Hooks;

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

use R3H6\Error404page\Hooks\ErrorHandler;
use R3H6\Error404page\Controller\ErrorPageController;

/**
 * Unit test for the ErrorHandler.
 */
class ErrorHandlerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var R3H6\Error404page\Hooks\ErrorHandler
     */
    protected $subject;

    /**
     * @var R3H6\Error404page\Controller\ErrorPageController;
     */
    protected $errorPageControllerMock;

    public function setUp()
    {
        parent::setUp();

        $this->errorPageControllerMock = $this->getMock(ErrorPageController::class, ['handleError'], [], '', false);

        $this->subject = $this->getMock(ErrorHandler::class, ['getErrorPageController', 'getSystemLanguage'], [], '', false);
        $this->subject
            ->expects($this->any())
            ->method('getErrorPageController')
            ->will($this->returnValue($this->errorPageControllerMock));
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->subject, $this->errorPageControllerMock);
    }

    /**
     * @test
     */
    public function pageNotFoundCallsHandleErrorOnErrorPageController()
    {


    }
}

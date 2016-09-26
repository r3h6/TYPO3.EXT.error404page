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

use R3H6\Error404page\Domain\Hook\ErrorHandlerHook;
use R3H6\Error404page\Domain\Handler\ErrorHandler;
use R3H6\Error404page\Domain\Model\Error;

/**
 * Unit test for the ErrorHandlerHook.
 */
class ErrorHandlerHookTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var R3H6\Error404page\Domain\Hook\ErrorHandlerHook
     */
    protected $subject;

    /**
     * @var R3H6\Error404page\Domain\Handler\ErrorHandler;
     */
    protected $errorHandlerMock;

    public function setUp()
    {
        parent::setUp();

        $this->errorHandlerMock = $this->getMock(ErrorHandler::class, ['handleError'], [], '', false);

        $this->subject = $this->getMock(ErrorHandlerHook::class, ['getErrorHandler', 'getSystemLanguage'], [], '', false);
        $this->subject
            ->expects($this->any())
            ->method('getErrorHandler')
            ->will($this->returnValue($this->errorHandlerMock));
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->subject, $this->errorHandlerMock);
    }

    /**
     * @test
     */
    public function pageNotFoundCallsHandleErrorOnErrorHandlerAndReturnsTheResult()
    {
        $this->errorHandlerMock
            ->expects($this->once())
            ->method('handleError')
            ->with($this->callback(function ($error) {
                return $error instanceof Error;
            }))
            ->will($this->returnValue('test'));

        $tsfeFixture = $this->getMock(\TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController::class, [], [], '', false);
        $paramsFixture = [];

        $this->assertSame('test', $this->subject->pageNotFound($paramsFixture, $tsfeFixture));
    }


    /**
     * @test
     */
    public function pageNotFoundSetsStatusForbiddenOnError()
    {
        $this->errorHandlerMock
            ->expects($this->once())
            ->method('handleError')
            ->with($this->callback(function ($error) {
                return $error->getStatusCode() === Error::STATUS_CODE_FORBIDDEN;
            }));

        $tsfeFixture = $this->getMock(\TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController::class, [], [], '', false);
        $paramsFixture = array (
            'currentUrl' => '/index.php?id=3',
            'reasonText' => 'ID was not an accessible page',
            'pageAccessFailureReasons' => array (
                'fe_group' => array (
                    3 => '-2',
                ),
            ),
        );

        $this->subject->pageNotFound($paramsFixture, $tsfeFixture);
    }
}

/*

array (
  'currentUrl' => '/index.php?id=10',
  'reasonText' => 'ID was not an accessible page',
  'pageAccessFailureReasons' =>
  array (
    'starttime' =>
    array (
      10 => '1501452000',
    ),
  ),
)

array (
  'currentUrl' => '/index.php?id=9',
  'reasonText' => 'ID was not an accessible page',
  'pageAccessFailureReasons' =>
  array (
    'endtime' =>
    array (
      9 => '1470002400',
    ),
  ),
)

array (
  'currentUrl' => '/index.php?id=7',
  'reasonText' => 'ID was not an accessible page',
  'pageAccessFailureReasons' =>
  array (
    'hidden' =>
    array (
      7 => true,
    ),
  ),
)

// Sys folder
array (
  'currentUrl' => '/index.php?id=5',
  'reasonText' => 'ID was not an accessible page',
  'pageAccessFailureReasons' =>
  array (
  ),
)

array (
  'currentUrl' => '/index.php?id=6',
  'reasonText' => 'ID was not an accessible page',
  'pageAccessFailureReasons' =>
  array (
    'fe_group' =>
    array (
      6 => '1',
    ),
  ),
)

array (
  'currentUrl' => '/index.php?id=3',
  'reasonText' => 'ID was not an accessible page',
  'pageAccessFailureReasons' =>
  array (
    'fe_group' =>
    array (
      3 => '-2',
    ),
  ),
)

array (
  'currentUrl' => '/index.php?id=11',
  'reasonText' => 'Subsection was found and not accessible',
  'pageAccessFailureReasons' =>
  array (
    'fe_group' =>
    array (
      3 => '-2',
    ),
  ),
)

 */
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
use R3H6\Error404page\Domain\Model\Error;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Unit test for the ErrorHandlerHook.
 */
class ErrorHandlerHookTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \R3H6\Error404page\Domain\Hook\ErrorHandlerHook
     */
    protected $subject;

    /**
     * @var \R3H6\Error404page\Domain\Handler\ErrorHandler;
     */
    protected $errorHandlerMock;

    public function setUp()
    {
        parent::setUp();

        $this->errorHandlerMock = $this->getMock('R3H6\\Error404page\\Domain\\Handler\\ErrorHandler', array('handleError'), array(), '', false);

        $this->subject = $this->getMock('R3H6\\Error404page\\Domain\\Hook\\ErrorHandlerHook', array('getErrorHandler', 'getSystemLanguage'), array(), '', false);
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
        $tsfeFixture = $this->getMock('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController', array(), array(), '', false);
        $paramsFixture = array(
            'currentUrl' => '/index.php?id=5',
            'reasonText' => 'ID was not an accessible page',
            'pageAccessFailureReasons' => array(),
        );

        $this->errorHandlerMock
            ->expects($this->once())
            ->method('handleError')
            ->with($this->callback(function ($error) use ($paramsFixture) {
                if (!$error instanceof Error) {
                    return false;
                }
                if ($error->getCurrentUrl() !== $paramsFixture['currentUrl']) {
                    return false;
                }
                if ($error->getReasonText() !== $paramsFixture['reasonText']) {
                    return false;
                }
                if ($error->getLanguage() !== 1) {
                    return false;
                }
                if ($error->getUrl() !== GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL')) {
                    return false;
                }
                if ($error->getReferer() !== GeneralUtility::getIndpEnv('HTTP_REFERER')) {
                    return false;
                }
                if ($error->getUserAgent() !== GeneralUtility::getIndpEnv('HTTP_USER_AGENT')) {
                    return false;
                }
                if ($error->getIp() !== GeneralUtility::getIndpEnv('REMOTE_ADDR')) {
                    return false;
                }
                if ($error->getHost() !== GeneralUtility::getIndpEnv('HTTP_HOST')) {
                    return false;
                }
                if ($error->getStatusCode() !== Error::STATUS_CODE_NOT_FOUND) {
                    return false;
                }
                if ($error->getPid() !== null) {
                    return false;
                }

                return true;
            }))
            ->will($this->returnValue('test'));

        $this->subject
            ->expects($this->once())
            ->method('getSystemLanguage')
            ->will($this->returnValue(1));

        $this->assertSame('test', $this->subject->pageNotFound($paramsFixture, $tsfeFixture));
    }

    /**
     * @test
     */
    public function pageNotFoundSetsPidAndStatusCodeOnError()
    {
        $tsfeFixture = $this->getMock('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController', array(), array(), '', false);
        $tsfeFixture->page = array('uid' => '123');

        $paramsFixture = array(
            'currentUrl' => '/index.php?id=3',
            'reasonText' => 'ID was not an accessible page',
            'pageAccessFailureReasons' => array(
                'fe_group' => array(
                    3 => '-2',
                ),
            ),
        );

        $this->errorHandlerMock
            ->expects($this->once())
            ->method('handleError')
            ->with($this->callback(function ($error) use ($tsfeFixture) {
                if ($error->getPid() !== (int) $tsfeFixture->page['uid']) {
                    return false;
                }
                if ($error->getStatusCode() !== Error::STATUS_CODE_FORBIDDEN) {
                    return false;
                }

                return true;
            }));

        $this->subject->pageNotFound($paramsFixture, $tsfeFixture);
    }

    public function pageNotFoundSetsNotStatusCodeOnError()
    {
        $tsfeFixture = $this->getMock('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController', array(), array(), '', false);

        $paramsFixture = array(
            'currentUrl' => '/invalid/path/',
            'reasonText' => 'Segment "path" was not a keyword for a postVarSet as expected on page with id=1.',
            'pageAccessFailureReasons' => array(
                'fe_group' => array(
                    '' => 0,
                    ),
                ),
            );

        $this->errorHandlerMock
            ->expects($this->once())
            ->method('handleError')
            ->with($this->callback(function ($error) {
                return $error->getStatusCode() !== Error::STATUS_CODE_FORBIDDEN;
            }));

        $this->subject->pageNotFound($paramsFixture, $tsfeFixture);
    }

    /**
     * @test
     */
    public function pageNotFoundSetsStatusForbiddenOnError()
    {
        $tsfeFixture = $this->getMock('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController', array(), array(), '', false);
        $tsfeFixture->page = array('uid' => '123');

        $paramsFixture = array(
            'currentUrl' => '/index.php?id=3',
            'reasonText' => 'ID was not an accessible page',
            'pageAccessFailureReasons' => array(
                'fe_group' => array(
                    3 => '-2',
                ),
            ),
        );

        $this->errorHandlerMock
            ->expects($this->once())
            ->method('handleError')
            ->with($this->callback(function ($error) {
                return $error->getStatusCode() === Error::STATUS_CODE_FORBIDDEN;
            }));

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


array (
  'currentUrl' => '/invalid/path/',
  'reasonText' => 'Segment "path" was not a keyword for a postVarSet as expected on page with id=1.',
  'pageAccessFailureReasons' =>
  array (
    'fe_group' =>
    array (
      '' => 0,
    ),
  ),
)

 */

<?php

namespace R3H6\Error404page\Tests\Unit\Controller;

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

use R3H6\Error404page\Domain\Model\Error;
use R3H6\Error404page\Domain\Handler\RedirectErrorHandler;

/**
 * Unit test for the ErrorHandler.
 */
class RedirectErrorHandlerTest extends \R3H6\Error404page\Tests\Unit\UnitTestCase
{
    /**
     * @var \R3H6\Error404page\Domain\Handler\RedirectErrorHandler
     */
    protected $subject;

    /**
     * @var \R3H6\Error404page\Domain\Repository\PageRepository
     */
    protected $pageRepositoryMock;

    /**
     * @var \R3H6\Error404page\Configuration\PageTsConfigManager
     */
    protected $pageTsConfigManagerMock;

    /**
     * @var \R3H6\Error404page\Facade\FrontendUser
     */
    protected $frontendUserMock;

    /**
     * @var \R3H6\Error404page\Facade\FrontendController
     */
    protected $frontendControllerMock;

    /**
     * @var \R3H6\Error404page\Service\HttpService
     */
    protected $httpServiceMock;

    public function setUp()
    {
        parent::setUp();
        $this->setUpTYPO3Globals();

        $this->subject = new RedirectErrorHandler();

        // Mock dependencies
        $this->pageRepositoryMock = $this->getMock('R3H6\\Error404page\\Domain\\Repository\\PageRepository', get_class_methods('R3H6\\Error404page\\Domain\\Repository\\PageRepository'), array(), '', false);
        $this->inject($this->subject, 'pageRepository', $this->pageRepositoryMock);

        $this->pageTsConfigManagerMock = $this->getMock('R3H6\\Error404page\\Configuration\\PageTsConfigManager', get_class_methods('R3H6\\Error404page\\Configuration\\PageTsConfigManager'), array(), '', false);
        $this->inject($this->subject, 'pageTsConfigManager', $this->pageTsConfigManagerMock);

        $this->frontendUserMock = $this->getMock('R3H6\\Error404page\\Facade\\FrontendUser', get_class_methods('R3H6\\Error404page\\Facade\\FrontendUser'), array(), '', false);
        $this->inject($this->subject, 'frontendUser', $this->frontendUserMock);

        $this->frontendControllerMock = $this->getMock('R3H6\\Error404page\\Facade\\FrontendController', get_class_methods('R3H6\\Error404page\\Facade\\FrontendController'), array(), '', false);
        $this->inject($this->subject, 'frontendController', $this->frontendControllerMock);

        $this->httpServiceMock = $this->getMock('R3H6\\Error404page\\Service\\HttpService', get_class_methods('R3H6\\Error404page\\Service\\HttpService'), array(), '', false);
        $this->inject($this->subject, 'httpService', $this->httpServiceMock);
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->tearDownTYPO3Globals();
        unset(
            $this->subject,
            $this->pageRepositoryMock,
            $this->pageTsConfigManagerMock,
            $this->frontendUserMock,
            $this->frontendControllerMock,
            $this->httpServiceMock
        );
    }

    /**
     * @test
     */
    public function handleErrorReturnsFalseIfErrorStatusCodeIsNotForbidden()
    {
        /** @var \R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = $this->getErrorFixture();
        $errorFixture->setStatusCode(Error::STATUS_CODE_NOT_FOUND);

        $this->assertFalse($this->subject->handleError($errorFixture));
    }

    /**
     * @test
     */
    public function handleErrorReturnsFalseIfErrorPidIsNotSet()
    {
        /** @var \R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = $this->getErrorFixture();
        $errorFixture->setPid(0);

        $this->assertFalse($this->subject->handleError($errorFixture));
    }

    /**
     * @test
     */
    public function handleErrorReturnsFalseIfUserIsLoggedIn()
    {
        /** @var \R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = $this->getErrorFixture();

        $this->frontendUserMock
            ->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue(true));

        $this->assertFalse($this->subject->handleError($errorFixture));
    }

    /**
     * @test
     */
    public function handleErrorReturnsFalseIfParameterIsEmpty()
    {
        /** @var \R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = $this->getErrorFixture();

        $this->mockPageTsConfig($errorFixture, '');

        $this->assertFalse($this->subject->handleError($errorFixture));
    }

    public function handleErrorAppendsLanguageToParameter()
    {
        /** @var \R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = $this->getErrorFixture();

        $this->mockPageTsConfig($errorFixture, '123');

        $this->frontendControllerMock
            ->expects($this->once())
            ->method('isDefaultLanguage')
            ->will($this->returnValue(false));

        $this->frontendControllerMock
            ->expects($this->once())
            ->method('isDefaultLanguage')
            ->will($this->returnValue(true));

        $this->frontendControllerMock
            ->expects($this->once())
            ->method('getSystemLanguageUid')
            ->will($this->returnValue(1));

        $this->frontendControllerMock
            ->expects($this->once())
            ->method('typoLink')
            ->with($this->equalTo(array(
                'parameter' => '123 - - - &L=1',
                'forceAbsoluteUrl' => true,
            )));

        $this->subject->handleError($errorFixture);
    }

    public function handleErrorFindsLoginPageWhenConfigurationIsAuto()
    {
        $expected = 'http://www.typo3.org/login/';

        /** @var \R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = $this->getErrorFixture();

        /** @var \R3H6\Error404page\Domain\Model\Error $pageFixture */
        $pageFixture = new Page(array(
            'uid' => 123,
            'pid' => 1,
        ));

        $this->mockPageTsConfig($errorFixture, 'auto');

        $this->pageRepositoryMock
            ->expects($this->once())
            ->method('findLoginPageForError')
            ->with($errorFixture)
            ->will($this->returnValue(null));

        $this->frontendControllerMock
            ->expects($this->once())
            ->method('typoLink')
            ->with($this->equalTo(array(
                'parameter' => '123',
                'forceAbsoluteUrl' => true,
            )))
            ->will($this->returnValue($expected));

        $return = $this->subject->handleError($errorFixture);

        $this->assertStringStartsWith($expected, $this->subject->getCachingData());
        $this->assertStringEndsWith('?redirect_url='.$errorFixture->getUrl(), $this->subject->getCachingData());
        $this->assertContains('pageId_123', $this->subject->getCacheTags(), 'Invalid cache tags');

        $this->assertTrue($return, 'Error handler shoudl return true');
    }

    protected function mockPageTsConfig($errorFixture, $redirectError403To)
    {
        $pageTsConfigMock = $this->getMock('R3H6\\Error404page\\Configuration\\PageTsConfig', array('is', 'get'), array(), '', false);
        $pageTsConfigMock
            ->expects($this->once())
            ->method('get')
            ->with('redirectError403To')
            ->will($this->returnValue($value));

        $this->pageTsConfigManagerMock
            ->expects($this->once())
            ->method('getPageTsConfig')
            ->with($errorFixture->getPid())
            ->will($this->returnValue($pageTsConfigMock));
    }

    /**
     * [getErrorFixture description].
     *
     * @return \R3H6\Error404page\Domain\Model\Error
     */
    protected function getErrorFixture()
    {
        /** @var \R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = new Error();
        $errorFixture->setStatusCode(Error::STATUS_CODE_FORBIDDEN);
        $errorFixture->setPid(123);
        $errorFixture->setUrl('http://www.typo3.org/not/found/');

        return $errorFixture;
    }
}

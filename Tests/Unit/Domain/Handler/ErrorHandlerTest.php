<?php

namespace R3H6\Error404page\Tests\Unit\Domain\Handler;

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

use R3H6\Error404page\Domain\Handler\ErrorHandler;
use R3H6\Error404page\Domain\Model\Error;

/**
 * Unit test for the ErrorHandler.§
 */
class ErrorHandlerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \R3H6\Error404page\Domain\Handler\ErrorHandler
     */
    protected $subject;

    /**
     * @var \R3H6\Error404page\Domain\Repository\ErrorRepository
     */
    protected $errorRepositoryMock;

    /**
     * @var \R3H6\Error404page\Configuration\ExtensionConfiguration
     */
    protected $extensionConfigurationMock;

    /**
     * @var \R3H6\Error404page\Domain\Cache\ErrorHandlerCache
     */
    protected $errorHandlerCacheMock;

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManagerMock;

    /**
     * @var \R3H6\Error404page\Service\HttpService
     */
    protected $httpServiceMock;


    public function setUp()
    {
        parent::setUp();

        $this->subject = new ErrorHandler();

        // Mock dependencies
        $this->errorRepositoryMock = $this->getMock('R3H6\\Error404page\\Domain\\Repository\\ErrorRepository', get_class_methods('R3H6\\Error404page\\Domain\\Repository\\ErrorRepository'), array(), '', false);
        $this->inject($this->subject, 'errorRepository', $this->errorRepositoryMock);

        $this->extensionConfigurationMock = $this->getMock('R3H6\\Error404page\\Configuration\\ExtensionConfiguration', array('is', 'get'), array(), '', false);
        $this->inject($this->subject, 'extensionConfiguration', $this->extensionConfigurationMock);

        $this->errorHandlerCacheMock = $this->getMock('R3H6\\Error404page\\Domain\\Cache\\ErrorHandlerCache', get_class_methods('R3H6\\Error404page\\Domain\\Cache\\ErrorHandlerCache'), array(), '', false);
        $this->inject($this->subject, 'errorHandlerCache', $this->errorHandlerCacheMock);

        $this->objectManagerMock = $this->getMock('TYPO3\\CMS\\Extbase\\Object\\ObjectManager', get_class_methods('TYPO3\\CMS\\Extbase\\Object\\ObjectManager'), array(), '', false);
        $this->inject($this->subject, 'objectManager', $this->objectManagerMock);

        $this->httpServiceMock = $this->getMock('R3H6\\Error404page\\Service\\HttpService', get_class_methods('R3H6\\Error404page\\Service\\HttpService'), array(), '', false);
        $this->inject($this->subject, 'httpService', $this->httpServiceMock);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset(
            $this->subject,
            $this->errorRepositoryMock,
            $this->extensionConfigurationMock,
            $this->pageCacheMock
        );
    }

    /**
     * @test
     */
    public function handleErrorLogsErrorWhenConfigurationIsSet()
    {
        /** @var \R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = new Error();

        $this->mockGetErrorHandlers();

        $this->extensionConfigurationMock
            ->expects($this->once())
            ->method('is')
            ->with($this->equalTo('enableErrorLog'))
            ->will($this->returnValue(true));

        $this->errorRepositoryMock
            ->expects($this->once())
            ->method('log')
            ->with($errorFixture);

        $this->subject->handleError($errorFixture);
    }

     /**
     * @test
     * @expectedException Exception
     */
    public function handleErrorThrowsExceptionForOwnRequests()
    {
        /** @var \R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = new Error();

        $this->httpServiceMock
            ->expects($this->once())
            ->method('isOwnRequest')
            ->will($this->returnValue(true));

        $this->subject->handleError($errorFixture);
    }

    /**
     * @test
     */
    public function handleErrorDoesNotLogErrorWhenConfigurationIsNotSet()
    {
        /** @var \R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = new Error();

        $this->mockGetErrorHandlers();

        $this->errorRepositoryMock
            ->expects($this->never())
            ->method('log');

        $this->subject->handleError($errorFixture);
    }

    /**
     * @test
     */
    public function handleErrorGetsOutputFromCache()
    {
        $expected = 'TYPO3';

        /** @var \R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = new Error();

        $cacheIdentifierFixture = sha1(uniqid());

        $errorHandlerMock = $this->getMock('R3H6\\Error404page\\Domain\\Handler\\ErrorHandlerInterface');
        $errorHandlerMock
            ->expects($this->once())
            ->method('getOutput')
            ->will($this->returnValue($expected));

        $this->errorHandlerCacheMock
            ->expects($this->once())
            ->method('calculateCacheIdentifier')
            ->with($errorFixture)
            ->will($this->returnValue($cacheIdentifierFixture));

        $this->errorHandlerCacheMock
            ->expects($this->once())
            ->method('get')
            ->with($cacheIdentifierFixture)
            ->will($this->returnValue($errorHandlerMock));

        $this->assertSame($expected, $this->subject->handleError($errorFixture));
    }

    protected function mockGetErrorHandlers()
    {
         $this->objectManagerMock
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValue(
                $this->getMock('R3H6\\Error404page\\Domain\\Handler\\ErrorHandlerInterface')
            ));
    }
}

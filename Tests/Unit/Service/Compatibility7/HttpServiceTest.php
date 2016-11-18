<?php

namespace R3H6\Error404page\Tests\Unit\Service\Compatibility7;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use HTTP_Request2_Response;

/**
 * Test case for class \R3H6\Error404page\Service\HttpService.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author R3 H6 <r3h6@outlook.com>
 */
class HttpServiceTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \R3H6\Error404page\Service\Compatibility7\HttpService
     */
    protected $subject = null;

    /**
     * @var \R3H6\Error404page\Configuration\ExtensionConfiguration
     */
    protected $extensionConfigurationMock;

    public function setUp()
    {
        $this->subject = new \R3H6\Error404page\Service\Compatibility7\HttpService();

        $this->extensionConfigurationMock = $this->getMock('R3H6\\Error404page\\Configuration\\ExtensionConfiguration', array('has', 'get'), array(), '', false);
        $this->inject($this->subject, 'extensionConfiguration', $this->extensionConfigurationMock);
    }

    public function tearDown()
    {
        unset($this->subject, $this->extensionConfigurationMock);
    }

    /**
     * @test
     */
    public function readUrlReturnsResonseBody()
    {
        $url = 'index.php?id=123';
        $body = 'Error 404';
        $status = 200;

        $this->createRequestMock($this->createResponseMock($status, $body));
        $this->assertSame($body, $this->subject->readUrl($url));
    }

    /**
     * @test
     */
    public function readUrlReturnsNullOnError()
    {
        $url = 'index.php?id=123';
        $body = 'Error 404';
        $status = 404;

        $this->createRequestMock($this->createResponseMock($status, $body));
        $this->assertSame(null, $this->subject->readUrl($url));
    }

    /**
     * @test
     */
    public function readUrlReturnsNullOnException()
    {
        $url = 'index.php?id=123';
        $this->createRequestMock(new \Exception("Test", 1477081985));
        $this->assertSame(null, $this->subject->readUrl($url));
    }

    /**
     * @test
     */
    public function getHttpRequestAddsGetParameter()
    {
        $requestMock = $this->createRequestMock($this->createResponseMock(200, 'Error 404'));
        $this->subject->readUrl('index.php?id=123');
        $this->assertStringStartsWith('index.php?id=123&tx_error404page_request=', (string) $requestMock->getUrl());

        $requestMock = $this->createRequestMock($this->createResponseMock(200, 'Error 404'));
        $this->subject->readUrl('/page/');
        $this->assertStringStartsWith('/page/?tx_error404page_request=', (string) $requestMock->getUrl());
    }

    protected function createResponseMock($status, $body)
    {
        $responseMock = $this->getMock('HTTP_Request2_Response', array('getStatus', 'getBody', 'getReasonPhrase'), array(), '', false);
        $responseMock
            ->expects($this->any())
            ->method('getStatus')
            ->will($this->returnValue($status));
        $responseMock
            ->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($body));
        $responseMock
            ->expects($this->any())
            ->method('getReasonPhrase')
            ->will($this->returnValue('PHPUnit'));

        return $responseMock;
    }

    protected function createRequestMock($response)
    {
        $requestClass = 'TYPO3\\CMS\\Core\\Http\\HttpRequest';
        $requestMock = $this->getMock($requestClass, array('send'), array(), '', false);
        $method = ($response instanceof \Exception) ? 'throwException': 'returnValue';
        $requestMock
            ->expects($this->any())
            ->method('send')
            ->will($this->$method($response));

        GeneralUtility::addInstance($requestClass, $requestMock);
        return $requestMock;
    }
}

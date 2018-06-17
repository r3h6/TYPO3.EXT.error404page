<?php

namespace R3H6\Error404page\Tests\Unit\Service;

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

/**
 * Test case for class \R3H6\Error404page\Service\HttpService.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author R3 H6 <r3h6@outlook.com>
 */
class HttpServiceTest extends \Nimut\TestingFramework\TestCase\UnitTestCase
{
    use \R3H6\Error404page\Tests\TestCaseCompatibility;
    use \R3H6\Error404page\Tests\DeprecationLogTrait;

    /**
     * @var \R3H6\Error404page\Service\HttpService
     */
    protected $subject = null;

    /**
     * @var \R3H6\Error404page\Configuration\ExtensionConfiguration
     */
    protected $extensionConfigurationMock;

    /**
     * @var \GuzzleHttp\Handler\MockHandler
     */
    protected $mockHandler;

    public function setUp()
    {
        if (!class_exists('GuzzleHttp\\Handler\\MockHandler')) {
            $this->markTestSkipped('Class \GuzzleHttp\Handler\MockHandler not available.');
        }

        $this->subject = new \R3H6\Error404page\Service\HttpService();

        $this->extensionConfigurationMock = $this->getMock('R3H6\\Error404page\\Configuration\\ExtensionConfiguration', array('has', 'get'), array(), '', false);
        $this->inject($this->subject, 'extensionConfiguration', $this->extensionConfigurationMock);

        $this->mockHandler = new \GuzzleHttp\Handler\MockHandler();

        $GLOBALS['TYPO3_CONF_VARS']['HTTP'] = [
            'handler' => $this->mockHandler
        ];
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

        $responseFixture = new \GuzzleHttp\Psr7\Response($status, [], $body);
        $this->mockHandler->append($responseFixture);

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

        $responseFixture = new \GuzzleHttp\Psr7\Response($status, [], $body);
        $this->mockHandler->append($responseFixture);

        $this->assertSame(null, $this->subject->readUrl($url));
    }

    /**
     * @test
     */
    public function getHttpRequestAddsGetParameter()
    {
        $url = 'index.php?id=123';
        $body = 'Error 404';
        $status = 404;

        $responseFixture = new \GuzzleHttp\Psr7\Response($status, [], $body);

        /** @var \TYPO3\CMS\Core\Http\RequestFactory $requestFactoryMock */
        $requestFactoryMock = $this->getMock('TYPO3\\CMS\\Core\\Http\\RequestFactory', ['request'], [], '', false);
        $requestFactoryMock
            ->expects($this->once())
            ->method('request')
            ->with($this->stringStartsWith('index.php?id=123&tx_error404page_request='))
            ->will($this->returnValue($responseFixture));

        GeneralUtility::addInstance('TYPO3\\CMS\\Core\\Http\\RequestFactory', $requestFactoryMock);

        $this->subject->readUrl($url);
    }
}
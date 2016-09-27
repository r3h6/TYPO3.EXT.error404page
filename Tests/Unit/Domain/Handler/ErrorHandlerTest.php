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

// require_once __DIR__ . '/../Fixtures/Request.php';

use R3H6\Error404page\Domain\Model\Error;
use R3H6\Error404page\Domain\Model\Page;
use R3H6\Error404page\Domain\Repository\ErrorRepository;
use R3H6\Error404page\Domain\Repository\PageRepository;
use R3H6\Error404page\Configuration\ExtensionConfiguration;
use R3H6\Error404page\Configuration\PageTsConfigManager;
use R3H6\Error404page\Configuration\PageTsConfig;
use R3H6\Error404page\Domain\Handler\ErrorHandler;
use R3H6\Error404page\Cache\PageCache;
use R3H6\Error404page\Facade\FrontendUser;
use R3H6\Error404page\Facade\FrontendController;
use R3H6\Error404page\Service\HttpService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Unit test for the ErrorHandler.§
 */
class ErrorHandlerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var R3H6\Error404page\Domain\Handler\ErrorHandler
     */
    protected $subject;

    /**
     * @var \R3H6\Error404page\Domain\Repository\ErrorRepository
     */
    protected $errorRepositoryMock;

    /**
     * @var \R3H6\Error404page\Domain\Repository\PageRepository
     */
    protected $pageRepositoryMock;

    /**
     * @var \R3H6\Error404page\Configuration\ExtensionConfiguration
     */
    protected $extensionConfigurationMock;

    /**
     * @var \R3H6\Error404page\Configuration\PageTsConfigManager
     */
    protected $pageTsConfigManagerMock;

    /**
     * @var \R3H6\Error404page\Cache\PageCache
     */
    protected $pageCacheMock;

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

    protected $params = [
        'reasonText' => 'Unit test',
        'currentUrl' => '/invalid/path/',
    ];

    protected $host = 'localhost';

    public function setUp()
    {
        parent::setUp();

        $this->subject = new ErrorHandler();

        // Mock dependencies
        $this->errorRepositoryMock = $this->getMock(ErrorRepository::class, get_class_methods(ErrorRepository::class), [], '', false);
        $this->inject($this->subject, 'errorRepository', $this->errorRepositoryMock);

        $this->pageRepositoryMock = $this->getMock(PageRepository::class, get_class_methods(PageRepository::class), [], '', false);
        $this->inject($this->subject, 'pageRepository', $this->pageRepositoryMock);

        $this->extensionConfigurationMock = $this->getMock(ExtensionConfiguration::class, get_class_methods(ExtensionConfiguration::class), [], '', false);
        $this->inject($this->subject, 'extensionConfiguration', $this->extensionConfigurationMock);

        $this->pageTsConfigManagerMock = $this->getMock(PageTsConfigManager::class, get_class_methods(pageTsConfigManager::class), [], '', false);
        $this->inject($this->subject, 'pageTsConfigManager', $this->pageTsConfigManagerMock);

        $this->pageCacheMock = $this->getMock(PageCache::class, get_class_methods(PageCache::class), [], '', false);
        $this->inject($this->subject, 'pageCache', $this->pageCacheMock);

        $this->frontendUserMock = $this->getMock(FrontendUser::class, get_class_methods(FrontendUser::class), [], '', false);
        $this->inject($this->subject, 'frontendUser', $this->frontendUserMock);

        $this->frontendControllerMock = $this->getMock(FrontendController::class, get_class_methods(FrontendController::class), [], '', false);
        $this->inject($this->subject, 'frontendController', $this->frontendControllerMock);

        $this->httpServiceMock = $this->getMock(HttpService::class, get_class_methods(HttpService::class), [], '', false);
        $this->inject($this->subject, 'httpService', $this->httpServiceMock);

        // Mock TYPO3 objects
        $GLOBALS['TYPO3_DB'] = $this->getMock(\TYPO3\CMS\Core\Database\DatabaseConnection::class, get_class_methods(\TYPO3\CMS\Core\Database\DatabaseConnection::class), [], '', false);

        $configurationManager = $this->getMock(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::class, get_class_methods(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::class), [], '', false);
        GeneralUtility::setSingletonInstance(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::class, $configurationManager);

        $GLOBALS['TSFE'] = new \stdClass();
        $GLOBALS['TSFE']->sys_language_uid = 0;
        $GLOBALS['TSFE']->csConvObj = $this->getMock(\TYPO3\CMS\Core\Charset\CharsetConverter::class, get_class_methods(\TYPO3\CMS\Core\Charset\CharsetConverter::class), [], '', false);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset(
            $this->subject,
            $this->errorRepositoryMock,
            $this->pageRepositoryMock,
            $this->extensionConfigurationMock,
            $this->pageTsConfigManagerMock,
            $this->pageCacheMock,
            $this->frontendUserMock,
            $this->frontendControllerMock,
            $this->httpServiceMock,
            $GLOBALS['TYPO3_DB'],
            $GLOBALS['TSFE']
        );
    }

    /**
     * @test
     */
    public function handleErrorReturnsStandardErrorpageMessageWhenGetParamIsSet()
    {
        /** @var R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = new Error();

        $_GET['tx_error404page_request'] = uniqid();
        $response = $this->subject->handleError($errorFixture);
        $this->assertRegExp('#<title>Page Not Found</title>#i', $response, 'Response is not a standard error message.');
    }

    /**
     * @test
     */
    public function handleErrorDisplaysPageFromCache()
    {
        /** @var R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = new Error();

        $expected = 'Cache';

        $this->pageCacheMock
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($expected));

        $response = $this->subject->handleError($errorFixture);
        $this->assertSame($expected, $response, 'Content is not taken from cache!');
    }

    /**
     * @test
     */
    public function handleErrorRedirectsToUrlFromCache()
    {
        /** @var R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = new Error();

        $expected = 'http://www.typo3.org/';

        $this->pageCacheMock
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue('REDIRECT:'.$expected));

        $this->httpServiceMock
            ->expects($this->once())
            ->method('redirect')
            ->with($this->equalTo($expected));

        $this->assertNull($this->subject->handleError($errorFixture));
    }


    /**
     * @test
     */
    public function handleErrorRedirectsToUrl()
    {
        $redirectFixture = 'http://www.typo3.org/login.html';

        /** @var R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = new Error();
        $errorFixture->setPid(9);
        $errorFixture->setStatusCode(Error::STATUS_CODE_FORBIDDEN);
        $errorFixture->setUrl('http://www.typo3.org/forbidden.html');

        /** @var R3H6\Error404page\Domain\Model\Error $pageFixture */
        $pageFixture = new Page([
            'uid' => 123,
            'pid' => 1,
        ]);

        $expected = $redirectFixture.'?redirect_url='.$errorFixture->getUrl();
        $cacheIdentifierFixture = uniqid();

        $pageTsConfigMock = $this->getMock(PageTsConfig::class, ['is', 'get'], [], '', false);
        $pageTsConfigMock
            ->expects($this->once())
            ->method('is')
            ->with('redirectError403To')
            ->will($this->returnValue(true));

        $pageTsConfigMock
            ->expects($this->once())
            ->method('get')
            ->with('redirectError403To')
            ->will($this->returnValue('auto'));

        $this->pageTsConfigManagerMock
            ->expects($this->once())
            ->method('getPageTsConfig')
            ->with($errorFixture->getPid())
            ->will($this->returnValue($pageTsConfigMock));

        $this->frontendUserMock
            ->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue(false));

        $this->pageRepositoryMock
            ->expects($this->once())
            ->method('findLoginPageForError')
            ->with($this->equalTo($errorFixture))
            ->will($this->returnValue($pageFixture));

        $this->pageCacheMock
            ->expects($this->once())
            ->method('buildEntryIdentifierFromError')
            ->with($this->equalTo($errorFixture))
            ->will($this->returnValue($cacheIdentifierFixture));

        $this->pageCacheMock
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValue(false));

        $this->pageCacheMock
            ->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo($cacheIdentifierFixture),
                $this->equalTo('REDIRECT:'.$expected)
            );

        $this->frontendControllerMock
            ->expects($this->once())
            ->method('typoLink')
            ->with($this->equalTo([
                'parameter' => '123',
                'forceAbsoluteUrl' => true,
            ]))
            ->will($this->returnValue($redirectFixture));

        $this->httpServiceMock
            ->expects($this->once())
            ->method('redirect')
            ->with($this->equalTo($expected));

        $this->subject->handleError($errorFixture);
    }

    /**
     * @test
     */
    public function handleErrorReturnsStandardErrorPageWhenCacheIsFalseAndNoErrorPageWasFound()
    {
        /** @var R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = new Error();

        $this->pageCacheMock
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValue(false));

        $this->pageRepositoryMock
            ->expects($this->once())
            ->method('find404PageForError')
            ->with($this->equalTo($errorFixture))
            ->will($this->returnValue(null));

        $response = $this->subject->handleError($errorFixture);
        $this->assertRegExp('#<title>Page Not Found</title>#i', $response, 'Response is not a standard error message.');
    }

    /**
     * @test
     */
    public function handleErrorWillFindAndCacheAndReturnErrorPage()
    {
        /** @var R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = new Error();

        /** @var R3H6\Error404page\Domain\Model\Error $pageFixture */
        $pageFixture = new Page([
            'uid' => 123,
            'pid' => 1,

        ]);

        $cacheIdentifierFixture = uniqid();

        $expectedUrl = '/index.php?id=';
        $expected = 'Error';

        $this->pageRepositoryMock
            ->expects($this->once())
            ->method('find404PageForError')
            ->with($this->equalTo($errorFixture))
            ->will($this->returnValue($pageFixture));

        $this->pageCacheMock
            ->expects($this->once())
            ->method('buildEntryIdentifierFromError')
            ->with($this->equalTo($errorFixture))
            ->will($this->returnValue($cacheIdentifierFixture));

        $this->pageCacheMock
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo($cacheIdentifierFixture))
            ->will($this->returnValue(false));

        $this->pageCacheMock
            ->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo($cacheIdentifierFixture),
                $this->equalTo($expected)
            );

        $this->httpServiceMock
            ->expects($this->once())
            ->method('readUrl')
            ->with($this->matchesRegularExpression('#/index\.php\?id=123&type=0&L=0&tx_error404page_request=.+$#'))
            ->will($this->returnValue($expected));


        $this->subject->handleError($errorFixture);
    }

    /**
     * @return R3H6\Error404page\Domain\Model\Error
     */
    protected function getErrorFixture($statusCode = Err)
    {
        /** @var R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = new Error();
        $errorFixture->setStatusCode($statusCode);
    }
}

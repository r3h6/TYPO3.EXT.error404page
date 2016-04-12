<?php

namespace R3H6\Page404\Tests\Unit\Controller;

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

use R3H6\Page404\Controller\ErrorPageController;
use R3H6\Page404\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Unit test for the ErrorPageController.
 */
class ErrorPageControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var R3H6\Page404\Controller\ErrorPageController
     */
    protected $subject;

    /**
     * @var R3H6\Page404\Domain\Repository\PageRepository
     */
    protected $pageRepository;

    /**
     * @var TYPO3\CMS\Core\Cache\CacheManager
     */
    protected $cacheManager;

    /**
     * @var TYPO3\CMS\Core\Cache\Frontend\FrontendInterface
     */
    protected $pageCache;

    protected $params = [
        'reasonText' => 'Unit test',
        'currentUrl' => '/invalid/path/',
    ];

    protected $host = 'localhost';

    public function setUp()
    {
        parent::setUp();

        $this->subject = new ErrorPageController();

        $this->pageRepository = $this->getMock(PageRepository::class, [], [], '', false);
        $this->inject($this->subject, 'pageRepository', $this->pageRepository);

        $this->pageCache = $this->getMock(VariableFrontend::class, ['get', 'set'], [], '', false);
        $this->inject($this->subject, 'pageCache', $this->pageCache);

        $this->cacheManager = $this->getMock(CacheManager::class, ['getCache'], [], '', false);
        $this->cacheManager
            ->expects($this->any())
            ->method('getCache')
            ->will($this->returnValue($this->pageCache));
        $this->inject($this->subject, 'cacheManager', $this->cacheManager);

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
        unset($this->subject, $this->pageRepository, $this->pageCache, $this->cacheManager, $GLOBALS['TYPO3_DB'], $GLOBALS['TSFE']);
    }

    /**
     * @test
     */
    public function handleErrorReturnsStandardErrorpageMessageWhenGetParamIsSet()
    {
        $_GET['tx_page404_request'] = uniqid();
        $response = $this->subject->handleError($this->params, $this->host, 0);
        $this->assertRegExp('#<title>Page Not Found</title>#i', $response, 'Response is not a standard error message.');
    }

    /**
     * @test
     */
    public function handleErrorDisplaysPageFromCache()
    {
        $expected = 'Cache';

        $this->pageCache
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($expected));

        $response = $this->subject->handleError($this->params, $this->host, 0);
        $this->assertSame($expected, $response, 'Content is not taken from cache!');
    }

    /**
     * @test
     * @todo Test for different hosts...
     */
    public function handleErrorCallsCacheWithDifferentCacheIdentifiers()
    {
        $GLOBALS['STACK'] = [];

        $this->pageCache
            ->expects($this->exactly(2))
            ->method('get')
            ->with($this->callback(
                function ($subject) {
                    $GLOBALS['STACK'][] = $subject;
                    return true;
                }
            ));

        $this->subject->handleError($this->params, $this->host, 0);
        $this->subject->handleError($this->params, $this->host, 1);

        $this->assertNotSame(
            $GLOBALS['STACK'][0],
            $GLOBALS['STACK'][1],
            'Cache identifier must be different for different sys languages.'
        );

        unset($GLOBALS['STACK']);
    }

    /**
     * @test
     */
    public function handleErrorReturnsStandardErrorPageWhenCacheIsFalseAndNoErrorPageWasFound()
    {
        $this->pageCache
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValue(false));

        $this->pageRepository
            ->expects($this->once())
            ->method('findErrorPageByHost')
            ->with($this->equalTo($this->host))
            ->will($this->returnValue(null));

        $response = $this->subject->handleError($this->params, $this->host, 0);
        $this->assertRegExp('#<title>Page Not Found</title>#i', $response, 'Response is not a standard error message.');
    }

    /**
     * @test
     */
    public function handleErrorWillFindAndCacheAndReturnErrorPage()
    {
        require_once __DIR__ . '/../Fixtures/Request.php';

        $errorPage = [
            'uid' => rand(1, 99)
        ];

        $this->pageCache
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValue(false));

        $this->pageCache
            ->expects($this->once())
            ->method('set')
            ->with(
                $this->callback(
                    function ($subject) {
                        return is_string($subject);
                    }
                ),
                $this->callback(
                    function ($subject) {
                        return is_string($subject);
                    }
                ),
                $this->equalTo(['pageId_' . $errorPage['uid']])
            );

        $this->pageRepository
            ->expects($this->once())
            ->method('findErrorPageByHost')
            ->with($this->equalTo($this->host))
            ->will($this->returnValue($errorPage));

        $response = $this->subject->handleError($this->params, $this->host, 0);
        $this->assertRegExp('#<title>Error 404</title>#i', $response, 'Response is not the error page!');
        $this->assertRegExp('#' . $this->params['reasonText'] . '#i', $response, 'Marker ###REASON### is not replaced!');
        $this->assertRegExp('#' . $this->params['currentUrl'] . '#i', $response, 'Marker ###CURRENT_URL### marker is not replaced!');
        $this->assertRegExp('#url is valid#i', $response, 'Url must be like "?id=1&L=1&tx_page404_request=abc123"!');
    }
}

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

require_once __DIR__ . '/../Fixtures/Request.php';

use Monogon\Page404\Controller\ErrorPageController;
use Monogon\Page404\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Unit test for the ErrorPageController.
 */
class ErrorPageControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var Monogon\Page404\Controller\ErrorPageController
     */
    protected $subject;

    /**
     * @var Monogon\Page404\Domain\Repository\PageRepository
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

    public function setUp()
    {
        parent::setUp();

        $this->subject = new ErrorPageController();

        $this->pageRepository = $this->getMock(PageRepository::class, [], [], '', false);
        $this->inject($this->subject, 'pageRepository', $this->pageRepository);

        $this->pageCache = $this->getMock(VariableFrontend::class, ['get'], [], '', false);
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
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->subject, $this->pageRepository, $this->pageCache, $this->cacheManager, $GLOBALS['TYPO3_DB']);
    }

    /**
     * @test
     */
    public function handleErrorReturnsStandardErrorpageMessageWhenGetParamIsSet()
    {
        $_GET['tx_page404_request'] = uniqid();
        $response = $this->subject->handleError($this->params);
        $this->assertRegExp('#<title>Page Not Found</title>#i', $response, 'Response is not a standard error message.');
    }
}

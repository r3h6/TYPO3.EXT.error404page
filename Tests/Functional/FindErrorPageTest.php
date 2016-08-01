<?php

namespace R3H6\Error404page\Tests\Functional;

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

use R3H6\Error404page\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Functional test case for the FindErrorPage.
 */
class FindErrorPageTest extends \TYPO3\CMS\Core\Tests\FunctionalTestCase
{
    use \R3H6\Error404page\Tests\Functional\BasicFrontendEnvironmentTrait;

    protected $backupGlobals = false;

    /**
     * @var R3H6\Error404page\Domain\Repository\PageRepository
     */
    protected $pageRepository;

    protected $testExtensionsToLoad = array('typo3conf/ext/error404page');

    protected $configurationToUseInTestInstance = [
        'EXT' => [
            'extConf' => [
                'error404page' => 'a:1:{s:19:"doktypeError404page";s:3:"104";}',
            ],
        ],
    ];

    public function setUp()
    {
        parent::setUp();
        $this->setUpBasicFrontendEnvironment();
        $this->pageRepository = GeneralUtility::makeInstance(ObjectManager::class)->get(PageRepository::class);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->pageRepository);
    }


    /**
     * @test
     */
    public function findErrorPageByHostWillReturnNullBecauseNoErrorPageIsDefined()
    {
        $this->importDataSet('pages');
        $errorPage = $this->pageRepository->findErrorPageByHost('typo3.org');
        $this->assertSame(null, $errorPage, 'No error page should be found!');
    }

    /**
     * @test
     */
    public function findErrorPageByHostWillReturnTheFirstFoundErrorPage()
    {
        $this->importDataSet('pages');
        $this->importDataSet('test_first');
        $errorPage = $this->pageRepository->findErrorPageByHost('typo3.org');
        $this->assertInternalType('array', $errorPage, 'No error page found!');
        $this->assertEquals(404, $errorPage['uid'], 'Wrong page found!');
    }

    /**
     * @test
     */
    public function findErrorPageByHostWillReturnNullBecauseErrorPageIsNotAccessible()
    {
        $this->importDataSet('pages');
        $this->importDataSet('fe_groups');
        $this->importDataSet('test_access');
        $errorPage = $this->pageRepository->findErrorPageByHost('typo3.org');
        $this->assertSame(null, $errorPage, 'No error page should be found!');
    }

    /**
     * @test
     * @dataProvider findErrorPageByHostWillReturnErrorPageForHostsDataProvider
     */
    public function findErrorPageByHostWillReturnErrorPageForHosts($host, $expected)
    {
        $this->importDataSet('pages');
        $this->importDataSet('sys_domain');
        $this->importDataSet('test_hosts');
        $errorPage = $this->pageRepository->findErrorPageByHost($host);
        $this->assertInternalType('array', $errorPage, 'No error page found!');
        $this->assertEquals($expected, $errorPage['uid'], 'Wrong page found!');
    }

    public function findErrorPageByHostWillReturnErrorPageForHostsDataProvider()
    {
        return [
            ['www.test1.org', 14],
            ['www.test2.org', 21],
            ['www.test3.org', 31],
            ['typo3.org', 31],
        ];
    }

    protected function importDataSet($name)
    {
        parent::importDataSet(ORIGINAL_ROOT . 'typo3conf/ext/error404page/Tests/Functional/Fixtures/Database/' . $name . '.xml');
    }
}

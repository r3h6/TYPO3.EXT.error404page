<?php

namespace R3H6\Page404\Tests\Functional;

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

use R3H6\Page404\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Functional test case for the FindErrorPage.
 */
class FindErrorPageTest extends \TYPO3\CMS\Core\Tests\FunctionalTestCase
{
    use \R3H6\Page404\Tests\Functional\BasicFrontendEnvironmentTrait;

    /**
     * @var R3H6\Page404\Domain\Repository\PageRepository
     */
    protected $pageRepository;

    protected $testExtensionsToLoad = array('typo3conf/ext/page404');

    protected $configurationToUseInTestInstance = [
        'EXT' => [
            'extConf' => [
                'page404' => 'a:1:{s:14:"doktypePage404";s:3:"104";}',
            ],
        ],
    ];

    public function setUp()
    {
        parent::setUp();
        $this->setUpBasicFrontendEnvironment();
        $_GET['L'] = 1;
        $GLOBALS['TSFE']->sys_language_uid = $_GET['L'];
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
    public function findErrorPageByHostWillReturnLocalizedErrorPage()
    {
        $this->importDataSet('pages');
        $this->importDataSet('sys_language');
        $this->importDataSet('test_language');
        $errorPage = $this->pageRepository->findErrorPageByHost('typo3.org');
        $this->assertInternalType('array', $errorPage, 'No error page found!');
        $this->assertEquals('Fehler Seite', $errorPage['title'], 'Wrong page found!');
    }

    protected function importDataSet($name)
    {
        parent::importDataSet(ORIGINAL_ROOT . 'typo3conf/ext/page404/Tests/Functional/Fixtures/Database/' . $name . '.xml');
    }
}

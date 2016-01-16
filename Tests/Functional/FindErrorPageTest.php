<?php

namespace Monogon\Page404\Tests\Functional;

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

use Monogon\Page404\Domain\Repository\PageRepository;

/**
 * Functional test case for the FindErrorPage.
 */
class FindErrorPageTest extends \TYPO3\CMS\Core\Tests\FunctionalTestCase
{
    use \Monogon\Page404\Tests\Functional\BasicFrontendEnvironmentTrait;

    /**
     * @var Monogon\Page404\Domain\Repository\PageRepository
     */
    protected $pageRepository;

    public function setUp()
    {
        parent::setUp();
        $this->setUpBasicFrontendEnvironment();
        $this->pageRepository = new PageRepository();
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
        $this->importDataSet('pages.error_first');
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
        $this->importDataSet('pages.error_access');
        $errorPage = $this->pageRepository->findErrorPageByHost('typo3.org');
        $this->assertSame(null, $errorPage, 'No error page should be found!');
    }

    /**
     * @test
     */
    public function findErrorPageByHostWillReturnErrorPageForHosts()
    {
        //and subdomains
    }

    protected function importDataSet($name)
    {
        parent::importDataSet(ORIGINAL_ROOT . 'typo3conf/ext/page404/Tests/Functional/Fixtures/Database/' . $name . '.xml');
    }
}

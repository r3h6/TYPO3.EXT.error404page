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

use R3H6\Error404page\Domain\Model\Error;
use R3H6\Error404page\Domain\Model\Page;
use R3H6\Error404page\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Functional test case for the PageRepository.
 */
class PageRepositoryTest extends FunctionalTestCase
{
    // use R3H6\Error404page\Tests\Functional\BasicFrontendEnvironmentTrait;

    /**
     * @var \R3H6\Error404page\Domain\Repository\PageRepository
     */
    protected $pageRepository;

    protected $testExtensionsToLoad = array('typo3conf/ext/error404page');

    protected $configurationToUseInTestInstance = array(
        'EXT' => array(
            'extConf' => array(
                'error404page' => 'a:3:{s:19:"doktypeError404page";i:104;s:14:"enableErrorLog";i:0;s:17:"enable403redirect";i:0;}',
            ),
        ),
    );

    public function setUp()
    {
        parent::setUp();
        $this->setUpBasicFrontendEnvironment();
        $this->pageRepository = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager')->get('R3H6\\Error404page\\Domain\\Repository\\PageRepository');
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->pageRepository);
    }


    /**
     * @test
     */
    public function find404PageForErrorReturnsNullForError404IfThereIsNoPage404()
    {
        $this->importDataSet('pages');
        $errorPage = $this->pageRepository->find404PageForError($this->createError());
        $this->assertSame(null, $errorPage);
    }

    /**
     * @test
     */
    public function find404PageForErrorReturnsPage404ForError404()
    {
        $expected = 404;
        $this->importDataSet('pages');
        $this->importPageRecord(array(
            'uid' => $expected,
            'title' => 'Error Page',
            'pid' => 1,
            'doktype' => 104,
        ));
        $errorPage = $this->pageRepository->find404PageForError($this->createError());
        $this->assertInstanceOf('R3H6\\Error404page\\Domain\\Model\\Page', $errorPage);
        $this->assertSame($expected, $errorPage->getUid());
    }

    /**
     * @test
     */
    public function find404PageForErrorReturnsNullBecausePage404IsHidden()
    {
        $this->importDataSet('pages');
        $this->importPageRecord(array(
            'title' => 'Error Page',
            'pid' => 1,
            'doktype' => 104,
            'hidden' => 1,
        ));
        $errorPage = $this->pageRepository->find404PageForError($this->createError());
        $this->assertSame(null, $errorPage);
    }

    /**
     * @test
     */
    public function find404PageForErrorReturnsNullBecausePage404IsRestricted()
    {
        $this->importDataSet('pages');
        $this->importPageRecord(array(
            'title' => 'Error Page',
            'pid' => 1,
            'doktype' => 104,
            'fe_group' => '1',
        ));
        $errorPage = $this->pageRepository->find404PageForError($this->createError());
        $this->assertSame(null, $errorPage);
    }

    /**
     * @test
     */
    public function find404PageForErrorReturnsNullBecausePage404HasDifferentHost()
    {
        $this->importDataSet('pages');
        $this->importDataSet('sys_domain');
        $this->importPageRecord(array(
            'title' => 'Error Page',
            'pid' => 2,
            'doktype' => 104,
        ));
        $errorPage = $this->pageRepository->find404PageForError($this->createError());
        $this->assertSame(null, $errorPage);
    }

    /**
     * @test
     */
    public function find404PageForErrorReturnsPage404ForDomain()
    {
        $expected = 404;
        $this->importDataSet('pages');
        $this->importDataSet('sys_domain');
        $this->importPageRecord(array(
            'uid' => 400,
            'title' => 'Error Page For Other Domain',
            'pid' => 2,
            'doktype' => 104,
        ));
        $this->importPageRecord(array(
            'uid' => $expected,
            'title' => 'Error Page',
            'pid' => 3,
            'doktype' => 104,
        ));
        $errorPage = $this->pageRepository->find404PageForError($this->createError('typo3.org'));
        $this->assertInstanceOf('R3H6\\Error404page\\Domain\\Model\\Page', $errorPage);
        $this->assertSame($expected, $errorPage->getUid());
    }

    /**
     * @test
     */
    public function find404PageForErrorWillReturnLocalizedErrorPage()
    {
        /** @var \R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = $this->createError();
        $errorFixture->setLanguage(1);

        $pageRepository = $this->getInaccessibleProperty($this->pageRepository, 'pageRepository');
        $pageRepository->sys_language_uid = 1;

        $this->importDataSet('pages');
        $this->importDataSet('sys_language');
        $this->importPageRecord(array(
            'uid' => 400,
            'title' => 'Error Page',
            'pid' => 1,
            'doktype' => 104,
        ));
        $this->importPageLanguageOverlayRecord(array(
            'uid' => 1,
            'title' => 'Fehler Seite',
            'pid' => 400,
            'doktype' => 104,
            'sys_language_uid' => 1,
            't3_origuid' => 0,
        ));

        $errorPage = $this->pageRepository->find404PageForError($errorFixture);
        $this->assertInstanceOf('R3H6\\Error404page\\Domain\\Model\\Page', $errorPage);
        $this->assertEquals('Fehler Seite', $errorPage->getTitle(), 'Wrong page found!');
    }

    protected function getInaccessibleProperty($object, $propertyName)
    {
        $reflectionObject = new \ReflectionObject($object);
        $reflectionProperty = $reflectionObject->getProperty($propertyName);
        $reflectionProperty->setAccessible(true);
        return $reflectionProperty->getValue($object);
    }

    /**
     * @return  R3H6\Error404page\Domain\Model\Error
     */
    protected function createError($host = 'www.typo3.org', $statusCode = Error::STATUS_CODE_NOT_FOUND, $pid = null)
    {
        /** @var \R3H6\Error404page\Domain\Model\Error   $error */
        $error = new Error();
        $error->setStatusCode($statusCode);
        $error->setPid($pid);
        $error->setHost($host);
        return $error;
    }

    protected function importPageRecord(array $record)
    {
        $this->getDatabaseConnection()->exec_INSERTquery('pages', $record);
    }
    protected function importPageLanguageOverlayRecord(array $record)
    {
        $this->getDatabaseConnection()->exec_INSERTquery('pages_language_overlay', $record);
    }


    protected function importDataSet($name)
    {
        parent::importDataSet(ORIGINAL_ROOT . 'typo3conf/ext/error404page/Tests/Functional/Fixtures/Database/' . $name . '.xml');
    }
}

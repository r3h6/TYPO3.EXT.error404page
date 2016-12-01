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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use R3H6\Error404page\Tests\Functional\Fixtures\ErrorHandlerFixture;

/**
 * Functional test case for the PageRepository.
 */
class CachingTest extends FunctionalTestCase
{
    // use R3H6\Error404page\Tests\Functional\BasicFrontendEnvironmentTrait;

    /**
     * @var \R3H6\Error404page\Domain\Cache\ErrorHandlerCache
     */
    protected $subject;

    protected $testExtensionsToLoad = array('typo3conf/ext/error404page');

    public function setUp()
    {
        parent::setUp();
        $this->setUpBasicFrontendEnvironment();
        $this->subject = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager')->get('R3H6\\Error404page\\Domain\\Cache\\ErrorHandlerCache');
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->subject);
    }

    /**
     * @test
     */
    public function setAndGet()
    {
        $expected = uniqid();

        /** @var \R3H6\Error404page\Domain\Handler\ErrorHandlerInterface $errorHandlerFixture */
        $errorHandlerFixture = new ErrorHandlerFixture($expected);

        $cacheIdentifierFixture = sha1(uniqid());

        $this->subject->set($cacheIdentifierFixture, $errorHandlerFixture);

        $this->assertSame(1, $this->getDatabaseConnection()->exec_SELECTcountRows('*', 'cf_cache_pages'));
        $this->assertSame(2, $this->getDatabaseConnection()->exec_SELECTcountRows('*', 'cf_cache_pages_tags'));

        $errorHandler = $this->subject->get($cacheIdentifierFixture);
        $this->assertNotSame($errorHandlerFixture, $errorHandler, 'Object from cache is not a new instance');
        $this->assertInstanceOf('R3H6\\Error404page\\Tests\\Functional\\Fixtures\\ErrorHandlerFixture', $errorHandler);
        $this->assertSame($expected, $errorHandler->getCachingData());
    }
}

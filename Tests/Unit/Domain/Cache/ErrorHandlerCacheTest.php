<?php

namespace R3H6\Error404page\Tests\Unit\Domain\Handler;

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

use R3H6\Error404page\Domain\Cache\ErrorHandlerCache;
use R3H6\Error404page\Domain\Model\Error;

/**
 * Unit test for the ErrorHandler.§.
 */
class ErrorHandlerCacheTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    use \R3H6\Error404page\Tests\TestCaseCompatibility;
    use \R3H6\Error404page\Tests\DeprecationLogTrait;

    /**
     * @var \R3H6\Error404page\Domain\Cache\ErrorHandlerCache
     */
    protected $subject;

    /**
     * @var \R3H6\Error404page\Facade\FrontendUser
     */
    protected $frontendUserMock;

    public function setUp()
    {
        parent::setUp();

        $this->subject = new ErrorHandlerCache();

        $this->frontendUserMock = $this->getMock('R3H6\\Error404page\\Facade\\FrontendUser', get_class_methods('R3H6\\Error404page\\Facade\\FrontendUser'), array(), '', false);
        $this->inject($this->subject, 'frontendUser', $this->frontendUserMock);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset(
            $this->subject,
            $this->frontendUserMock
        );
    }

    /**
     * @test
     */
    public function calculateCacheIdentifierReturnsSameResultForSameError()
    {
        /** @var \R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = new Error();

        $cacheIdentifier1 = $this->subject->calculateCacheIdentifier($errorFixture);
        sleep(1);
        $cacheIdentifier2 = $this->subject->calculateCacheIdentifier(clone $errorFixture);

        $this->assertEquals($cacheIdentifier1, $cacheIdentifier2);
    }

    /**
     * @test
     */
    public function calculateCacheIdentifierIgnoresCurrentUrlForPageNotFoundErrors()
    {
        /** @var \R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = new Error();
        $errorFixture->setStatusCode(Error::STATUS_CODE_NOT_FOUND);

        $cacheIdentifier1 = $this->subject->calculateCacheIdentifier($errorFixture);

        $errorFixture->setCurrentUrl('page/not/found.html');
        $cacheIdentifier2 = $this->subject->calculateCacheIdentifier($errorFixture);

        $this->assertEquals($cacheIdentifier1, $cacheIdentifier2);
    }

    /**
     * @test
     */
    public function calculateCacheIdentifierRespectsCurrentUrlForForbiddenErrors()
    {
        /** @var \R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = new Error();
        $errorFixture->setStatusCode(Error::STATUS_CODE_FORBIDDEN);

        $cacheIdentifier1 = $this->subject->calculateCacheIdentifier($errorFixture);

        $errorFixture->setCurrentUrl('page/not/found.html');
        $cacheIdentifier2 = $this->subject->calculateCacheIdentifier($errorFixture);

        $this->assertNotEquals($cacheIdentifier1, $cacheIdentifier2);
    }
}

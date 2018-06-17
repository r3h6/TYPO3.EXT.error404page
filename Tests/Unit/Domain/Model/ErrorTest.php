<?php

namespace R3H6\Error404page\Tests\Unit\Domain\Model;

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

/**
 * Test case for class \R3H6\Error404page\Domain\Model\Error.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @author R3 H6 <r3h6@outlook.com>
 */
class ErrorTest extends \Nimut\TestingFramework\TestCase\UnitTestCase
{
    use \R3H6\Error404page\Tests\TestCaseCompatibility;
    use \R3H6\Error404page\Tests\DeprecationLogTrait;

    /**
     * @var \R3H6\Error404page\Domain\Model\Error
     */
    protected $subject = null;

    public function setUp()
    {
        $this->subject = new \R3H6\Error404page\Domain\Model\Error();
    }

    public function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function getUrlReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getUrl()
        );
    }

    /**
     * @test
     */
    public function setUrlForStringSetsUrl()
    {
        $this->subject->setUrl('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'url',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getUrlHashReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getUrlHash()
        );
    }

    /**
     * @test
     */
    public function getRootPageReturnsInitialValueForInt()
    {
        $this->assertSame(
            0,
            $this->subject->getRootPage()
        );
    }

    /**
     * @test
     */
    public function setRootPageForIntSetsRootPage()
    {
        $this->subject->setRootPage(123);

        $this->assertAttributeEquals(
            123,
            'rootPage',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getReasonReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getReason()
        );
    }

    /**
     * @test
     */
    public function setReasonForStringSetsReason()
    {
        $this->subject->setReason('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'reason',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getCounterReturnsInitialValueForInt()
    {
        $this->assertSame(
            0,
            $this->subject->getCounter()
        );
    }

    /**
     * @test
     */
    public function setCounterForIntSetsCounter()
    {
        $this->subject->setCounter(123);

        $this->assertAttributeEquals(
            123,
            'counter',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getRefererReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getReferer()
        );
    }

    /**
     * @test
     */
    public function setRefererForStringSetsReferer()
    {
        $this->subject->setReferer('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'referer',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getIpReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getIp()
        );
    }

    /**
     * @test
     */
    public function setIpForStringSetsIp()
    {
        $this->subject->setIp('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'ip',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getUserAgentReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getUserAgent()
        );
    }

    /**
     * @test
     */
    public function setUserAgentForStringSetsUserAgent()
    {
        $this->subject->setUserAgent('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'userAgent',
            $this->subject
        );
    }
}

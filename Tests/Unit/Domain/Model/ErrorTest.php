<?php

namespace R3H6\Error404page\Tests\Unit\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 R3 H6 <r3h6@outlook.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Test case for class \R3H6\Error404page\Domain\Model\Error.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author R3 H6 <r3h6@outlook.com>
 */
class ErrorTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
	/**
	 * @var \R3H6\Error404page\Domain\Model\Error
	 */
	protected $subject = NULL;

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
	public function getSha1ReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getSha1()
		);
	}

	/**
	 * @test
	 */
	public function setSha1ForStringSetsSha1()
	{
		$this->subject->setSha1('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'sha1',
			$this->subject
		);
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
	public function getLastRefererReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getLastReferer()
		);
	}

	/**
	 * @test
	 */
	public function setLastRefererForStringSetsLastReferer()
	{
		$this->subject->setLastReferer('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'lastReferer',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getCounterReturnsInitialValueForInt()
	{	}

	/**
	 * @test
	 */
	public function setCounterForIntSetsCounter()
	{	}
}

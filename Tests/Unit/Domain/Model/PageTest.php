<?php

namespace R3H6\Error404page\Tests\Unit\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;

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
 * Test case for class \R3H6\Error404page\Domain\Model\Page.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @author R3 H6 <r3h6@outlook.com>
 */
class PageTest extends \Nimut\TestingFramework\TestCase\UnitTestCase
{
    use \R3H6\Error404page\Tests\TestCaseCompatibility;
    use \R3H6\Error404page\Tests\DeprecationLogTrait;

    /**
     * @var \R3H6\Error404page\Domain\Model\Page
     */
    protected $subject = null;

    protected $dataFixture = array(
        'uid' => '99',
        'pid' => '11',
        'title' => 'Test',
        'no_cache' => '1',
    );

    public function setUp()
    {
        $this->subject = new \R3H6\Error404page\Domain\Model\Page($this->dataFixture);

        if (method_exists('TYPO3\\CMS\\Core\\Utility\\GeneralUtility', 'flushInternalRuntimeCaches')) {
            GeneralUtility::flushInternalRuntimeCaches();
        }
        $_SERVER['HTTP_HOST'] = 'typo3.org';
    }

    public function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function getTitle()
    {
        $this->assertSame(
            'Test',
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function useCache()
    {
        $this->assertSame(
            false,
            $this->subject->useCache()
        );
    }

    /**
     * @test
     *
     * @see https://github.com/r3h6/TYPO3.EXT.error404page/issues/7
     */
    public function getUrlWithoutTypeAndLanguage()
    {
        $this->assertSame(
            'http://typo3.org/index.php?id=99',
            $this->subject->getUrl()
        );
    }

    /**
     * @test
     *
     * @see https://github.com/r3h6/TYPO3.EXT.error404page/issues/7
     */
    public function getUrlWithTypeAndLanguageOverlayParameter()
    {
        $_GET['type'] = '2';

        $this->subject = new \R3H6\Error404page\Domain\Model\Page($this->dataFixture + array(
            '_PAGES_OVERLAY_LANGUAGE' => '3',
        ));

        $this->assertSame(
            'http://typo3.org/index.php?id=99&type=2&L=3',
            $this->subject->getUrl()
        );
    }
}

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

use R3H6\Error404page\Domain\Model\Error;
use R3H6\Error404page\Domain\Handler\Page404ErrorHandler;

/**
 * Unit test for the ErrorHandler.
 */
class Page404ErrorHandlerTest extends \R3H6\Error404page\Tests\Unit\UnitTestCase
{
    use \R3H6\Error404page\Tests\TestCaseCompatibility;
    use \R3H6\Error404page\Tests\DeprecationLogTrait;

    /**
     * @var \R3H6\Error404page\Domain\Handler\Page404ErrorHandler
     */
    protected $subject;

    /**
     * @var \R3H6\Error404page\Domain\Repository\PageRepository
     */
    protected $pageRepositoryMock;

    /**
     * @var \R3H6\Error404page\Service\HttpService
     */
    protected $httpServiceMock;

    public function setUp()
    {
        parent::setUp();
        $this->setUpTYPO3Globals();

        $this->subject = new Page404ErrorHandler();

        // Mock dependencies
        $this->pageRepositoryMock = $this->getMock('R3H6\\Error404page\\Domain\\Repository\\PageRepository', get_class_methods('R3H6\\Error404page\\Domain\\Repository\\PageRepository'), array(), '', false);
        $this->inject($this->subject, 'pageRepository', $this->pageRepositoryMock);

        $this->httpServiceMock = $this->getMock('R3H6\\Error404page\\Service\\HttpService', get_class_methods('R3H6\\Error404page\\Service\\HttpService'), array(), '', false);
        $this->inject($this->subject, 'httpService', $this->httpServiceMock);
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->tearDownTYPO3Globals();
        unset(
            $this->subject,
            $this->pageRepositoryMock,
            $this->httpServiceMock
        );
    }

    /**
     * @test
     */
    public function handleErrorReturnsFalseIfNoErrorPageIsFound()
    {
        /** @var \R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = new Error();

        $this->pageRepositoryMock
            ->expects($this->once())
            ->method('find404PageForError')
            ->with($errorFixture)
            ->will($this->returnValue(null));

        $this->assertFalse($this->subject->handleError($errorFixture));
    }

    /**
     * @test
     */
    public function handleErrorReturnsFalseWhenHttpServiceReturnsNullCalledWithPageUrl()
    {
        /** @var \R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = new Error();

        /** @var \R3H6\Error404page\Domain\Model\Error $pageMock */
        $pageMock = $this->getPageMock(404, 'test');

        $this->pageRepositoryMock
            ->expects($this->once())
            ->method('find404PageForError')
            ->with($errorFixture)
            ->will($this->returnValue($pageMock));

        $this->httpServiceMock
            ->expects($this->once())
            ->method('readUrl')
            ->with($this->equalTo('test'))
            ->will($this->returnValue(null));

        $this->assertFalse($this->subject->handleError($errorFixture));
    }

    /**
     * @test
     */
    public function handleErrorReturnsTrueIfPageIsFound()
    {
        $expected = 'Error 404';

        /** @var \R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = new Error();

        /** @var \R3H6\Error404page\Domain\Model\Error $pageMock */
        $pageMock = $this->getPageMock(404, 'test');

        $this->pageRepositoryMock
            ->expects($this->once())
            ->method('find404PageForError')
            ->with($errorFixture)
            ->will($this->returnValue($pageMock));

        $this->httpServiceMock
            ->expects($this->once())
            ->method('readUrl')
            ->with($this->equalTo('test'))
            ->will($this->returnValue($expected));

        $this->assertTrue($this->subject->handleError($errorFixture));
        $this->assertSame($expected, $this->subject->getCachingData());
        $this->assertContains('pageId_404', $this->subject->getCacheTags());
    }

    /**
     * @test
     */
    public function getOutputSubstituesMarkers()
    {
        /** @var \R3H6\Error404page\Domain\Model\Error $errorFixture */
        $errorFixture = new Error();
        $errorFixture->setCurrentUrl('http://www.typo3.org/page/not/found/');
        $errorFixture->setReasonText('Test reason text');

        $this->subject->setCachingData('
            <h1>Page "###CURRENT_URL###" not found</h1>
            <p>###REASON###</p>
            ');

        $output = $this->subject->getOutput($errorFixture);

        $this->assertContains($errorFixture->getCurrentUrl(), $output);
        $this->assertContains($errorFixture->getReasonText(), $output);
    }

    protected function getPageMock($uid, $url)
    {
        /** @var \R3H6\Error404page\Domain\Model\Error $pageMock */
        $pageMock = $this->getMock('R3H6\\Error404page\\Domain\\Model\\Page', array('getUid', 'getUrl'), array(), '', false);

        $pageMock
            ->expects($this->any())
            ->method('getUid')
            ->will($this->returnValue($uid));

        $pageMock
            ->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValue($url));

        return $pageMock;
    }
}

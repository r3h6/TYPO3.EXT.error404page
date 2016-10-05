<?php

namespace R3H6\Error404page\Tests\Functional\Fixtures;

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
 * Fixture
 */
class ErrorHandlerFixture implements \R3H6\Error404page\Domain\Handler\ErrorHandlerInterface
{
    protected $data = null;

    public function __construct($data = null)
    {
        $this->data = $data;
    }

    public function handleError(\R3H6\Error404page\Domain\Model\Error $error)
    {
        return true;
    }

    public function getOutput(\R3H6\Error404page\Domain\Model\Error $error)
    {
        return 'TYPO3';
    }

    public function setCachingData($data)
    {
        $this->data = $data;
    }

    public function getCachingData()
    {
        return $this->data;
    }

    public function getCacheTags()
    {
        return array('TYPO3', 'Test');
    }
}
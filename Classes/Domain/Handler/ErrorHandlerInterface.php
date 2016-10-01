<?php

namespace R3H6\Error404page\Domain\Handler;

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
 * ErrorHandlerInterface.
 */
interface ErrorHandlerInterface extends \R3H6\Error404page\Cache\CachableInterface
{
    /**
     * Handle error.
     *
     * @param \R3H6\Error404page\Domain\Model\Error $error
     * @return boolean true if error could be handled
     */
    public function handleError(\R3H6\Error404page\Domain\Model\Error $error);

    /**
     * Get the output.
     *
     * @param \R3H6\Error404page\Domain\Model\Error $error
     * @return string
     */
    public function getOutput(\R3H6\Error404page\Domain\Model\Error $error);
}

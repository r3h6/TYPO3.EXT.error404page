<?php

namespace R3H6\Error404page\Domain\Cache;

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
 * CachableInterface.
 */
interface CachableInterface
{
    /**
     * Get the data.
     *
     * @return string
     */
    public function setCachingData($data);

    /**
     * Get the data.
     *
     * @return string
     */
    public function getCachingData();

    /**
     * Get optional cache tags.
     *
     * @return array
     */
    public function getCacheTags();
}

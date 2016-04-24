<?php
namespace R3H6\Error404page\View\Error;

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
 * ChartJson
 */
class ChartJson extends \TYPO3\CMS\Extbase\Mvc\View\JsonView
{
    protected $variablesToRender = array('errors');

    // public function getReponseData()
    // {
    //     \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->variables);
    //     return $this->variables['errors'];
    // }
}

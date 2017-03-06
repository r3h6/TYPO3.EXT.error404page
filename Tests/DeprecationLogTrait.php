<?php

namespace R3H6\Error404page\Tests;

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
 * DeprecationLogTrait
 */
trait DeprecationLogTrait
{
    public function enableDeprecationLog()
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['enableDeprecationLog'] = 'devlog';
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['devLog']['error404page'] = function (array $params) {
            if (stripos($params['msg'], 'R3H6\\Error404page\\')) {
                throw new \Exception($params['msg'], 1482271675);
            }
        };
    }
}

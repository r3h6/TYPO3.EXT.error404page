<?php

namespace R3H6\Error404page\Configuration;

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
 * PageTsConfiguration
 *
 * API to access extension configuration (ext_conf_template.txt).
 */
class PageTsConfig
{
    protected $configuration = null;
    protected $pageUid = null;

    public function __construct(array $configuration, $pageUid)
    {
        $this->configuration = $configuration;
        $this->pageUid = $pageUid;

    }

    public function get($key)
    {
        return isset($this->configuration[$key]) ? $this->configuration[$key]: null;
    }

    public function is($key)
    {
        return empty($this->get($key)) === false;
    }
}

<?php
namespace R3H6\Error404page\Facade;

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
 * FrontendUser
 */
class FrontendUser
{
    /**
     * FrontendUserAuthentication
     *
     * @var TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
     */
    protected $frontendUserAuthentication = null;

    public function __construct()
    {
        $this->frontendUserAuthentication = $GLOBALS['TSFE']->fe_user;
    }

    public function isLoggedIn()
    {
        return $this->frontendUserAuthentication->user !== null;
    }

    public function getUserGroups()
    {
        if (!empty($this->frontendUserAuthentication->groupData['uid'])) {
            $groups = array_values($this->frontendUserAuthentication->groupData['uid']);
            sort($groups);
            return $groups;
        }
        return [];
        // $this->frontendUserAuthentication->fetchGroupData();
        // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this);
        // $this->frontendUserAuthentication->groupData;
    }
}

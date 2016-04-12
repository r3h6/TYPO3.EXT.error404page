<?php

namespace R3H6\Page404\Hooks;

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

use R3H6\Page404\Controller\ErrorPageController;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Error handler
 *
 * This is a bridge class for using extbase dependency injection.
 */
class ErrorHandler
{
    /**
     * [pageNotFound description]
     * @param  array                                                       $params
     * @param  \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $tsfe
     * @return string
     * @see \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController::pageErrorHandler
     */
    public function pageNotFound(array $params, \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $tsfe)
    {
        $host = GeneralUtility::getIndpEnv('REMOTE_HOST');
        $language = $this->getSystemLanguage();
        return GeneralUtility::makeInstance(ObjectManager::class)->get(ErrorPageController::class)->handleError($params, $host, $language);
    }

    /**
     * Get system language uid
     *
     * @return int
     */
    protected function getSystemLanguage()
    {
        return (int) GeneralUtility::_GP('L');
    }
}
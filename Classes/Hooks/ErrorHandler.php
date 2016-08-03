<?php

namespace R3H6\Error404page\Hooks;

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

use R3H6\Error404page\Controller\ErrorPageController;
use R3H6\Error404page\Domain\Model\Error;
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
        // $host = GeneralUtility::getIndpEnv('HTTP_HOST');
        // $language = $this->getSystemLanguage();

        /** @var R3H6\Error404page\Domain\Model\Error $error */
        $error = GeneralUtility::makeInstance(Error::class);
        $error->setReasonText($params['reasonText']);
        $error->setCurrentUrl($params['currentUrl']);
        $error->setLanguage($this->getSystemLanguage());
        $error->setUrl(GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'));
        $error->setReferer(GeneralUtility::getIndpEnv('HTTP_REFERER'));
        $error->setUserAgent(GeneralUtility::getIndpEnv('HTTP_USER_AGENT'));
        $error->setIp(GeneralUtility::getIndpEnv('REMOTE_ADDR'));
        $error->setHost(GeneralUtility::getIndpEnv('HTTP_HOST'));

        if (false === empty($tsfe->page)) {
            $error->setPid((int) $tsfe->page['uid']);
        }

        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($error);
        // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($tsfe);

        $this->getErrorPageController()->handleError($error);
    }

    /**
     * [getErrorPageController description]
     * @return R3H6\Error404page\Controller\ErrorPageController
     */
    protected function getErrorPageController()
    {
        return GeneralUtility::makeInstance(ObjectManager::class)->get(ErrorPageController::class);
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

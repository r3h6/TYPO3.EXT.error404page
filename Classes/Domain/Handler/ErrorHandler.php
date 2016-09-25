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

use R3H6\Error404page\Configuration\ExtensionConfiguration;
use R3H6\Error404page\Domain\Repository\PageRepository;
use R3H6\Error404page\Domain\Model\Error;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Messaging\ErrorpageMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Error handler.
 */
class ErrorHandler
{
    /**
     * @var \R3H6\Error404page\Domain\Repository\ErrorRepository
     * @inject
     */
    protected $errorRepository;

    /**
     * @var \R3H6\Error404page\Domain\Repository\PageRepository
     * @inject
     */
    protected $pageRepository;

    /**
     * @var \R3H6\Error404page\Configuration\ExtensionConfiguration
     * @inject
     */
    protected $extensionConfiguration;

    /**
     * @var \R3H6\Error404page\Configuration\PageTsConfigManager
     * @inject
     */
    protected $pageTsConfigManager;

    /**
     * @var \R3H6\Error404page\Cache\PageCache
     * @inject
     */
    protected $pageCache;

    /**
     * @var \R3H6\Error404page\Facade\FrontendUser
     * @inject
     */
    protected $frontendUser;

    /**
     * @var \R3H6\Error404page\Facade\FrontendController
     * @inject
     */
    protected $frontendController;

    /**
     * Renders the error page.
     *
     * @param  R3H6\Error404page\Domain\Model\Error $error
     * @return string Error page html.
     */
    public function handleError(Error $error)
    {
        if (!isset($_GET['tx_error404page_request'])) {
            if ($this->extensionConfiguration->get('enableErrorLog')) {
                $this->errorRepository->log($error);
            }

            $cacheIdentifier = $this->pageCache->buildEntryIdentifierFromError($error);
            $content = $this->pageCache->get($cacheIdentifier);

            if ($content === false) {
                /** @var R3H6\Error404page\Configuration\PageTsConfig $pageTsConfig */
                $pageTsConfig = $this->pageTsConfigManager->getPageTsConfig($error->getPid());

                // Get redirect link if it is a 403 error and user is not logged in and redirect is configured.
                if ($error->getStatusCode() === Error::STATUS_CODE_FORBIDDEN && !$this->frontendUser->isLoggedIn() &&
                    $pageTsConfig->has('redirectError403To')) {
                    $parameter = (string) $pageTsConfig->get('redirectError403To');

                    if ($parameter === 'auto') {
                        $loginPage = $this->pageRepository->findLoginPageForError($error);
                        if ($loginPage !== null) {
                            $parameter = (string) $loginPage->getUid();
                        } else {
                            $parameter = null;
                        }
                    }

                    if (MathUtility::canBeInterpretedAsInteger($parameter)) {
                        if (!$this->frontendController->isDefaultType()) {
                            $parameter .= ','.$this->frontendController->getType();
                        }
                        if (GeneralUtility::_GP('L') !== null || $this->frontendController->isDefaultGetVar('L')) {
                            $parameter .= ' - - - &L=' . $this->frontendController->getSystemLanguageUid();
                        }
                    }

                    if ($parameter) {
                        $content = 'REDIRECT:' . $this->frontendController->typoLink(['parameter' => $parameter, 'forceAbsoluteUrl' => true]);
                        $this->pageCache->set($cacheIdentifier, $content, $error->getPid());
                    }
                // Otherwise try to find a 404 page and display it.
                } else {
                    $errorPage = $this->pageRepository->find404PageForError($error);
                    if ($errorPage !== null) {
                        $content = $errorPage->getContent();
                        if ($content !== null && $errorPage->useCache()) {
                            $this->pageCache->set($cacheIdentifier, $content, $errorPage->getUid());
                        }
                    }
                }
            }

            if (is_string($content) && strlen($content)) {
                if (strpos($content, 'REDIRECT:') === 0) {
                    HttpUtility::redirect(substr($content, 9));
                }

                $replaceMap = [
                    '###CURRENT_URL###' => $error->getCurrentUrl(),
                    '###REASON###' => $error->getReasonText(),
                    '###ERROR_STATUS_CODE###' => $error->getStatusCode(),
                ];

                return str_replace(array_keys($replaceMap), array_values($replaceMap), $content);
            }
        }

        // Fallback to core error message.
        $title = 'Page Not Found';
        $message = 'The page did not exist or was inaccessible.' . ($error->getReasonText() ? ' Reason: ' . htmlspecialchars($error->getReasonText()) : '');
        $messagePage = GeneralUtility::makeInstance(ErrorpageMessage::class, $message, $title);
        return $messagePage->render();
    }
}

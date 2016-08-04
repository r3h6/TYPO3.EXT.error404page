<?php

namespace R3H6\Error404page\Controller;

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
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Error page controller.
 */
class ErrorPageController
{
    const LOCALLANG = 'LLL:EXT:error404page/Resources/Private/Language/locallang.xlf';

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
     * @var \R3H6\Error404page\Cache\PageCache
     * @inject
     */
    protected $pageCache;

    /**
     * Renders the error page.
     *
     * @param  R3H6\Error404page\Domain\Model\Error $error
     * @return string Error page html.
     */
    public function handleError(Error $error)
    {
        // $currentUrl = $params['currentUrl'];
        // $reason = LocalizationUtility::translate('reasonText.' . sha1($params['reasonText']), 'error404page');
        // if ($reason === null) {
        //     $reason = $params['reasonText'];
        // }

        if (!isset($_GET['tx_error404page_request'])) {

            if ($this->extensionConfiguration->get('enableErrorLog')) {
                $this->errorRepository->log($error);
            }

            $cacheIdentifier = $this->pageCache->buildEntryIdentifierFromError($error);

            $content = $this->pageCache->get($cacheIdentifier);
            if ($content === false) {
                // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($error);
                $errorPage = $this->pageRepository->findOneByError($error);
                // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($errorPage);
                // throw new \Exception("Error Processing Request", 1);
// exit;
                if ($errorPage !== null) {
                    $content = $errorPage->getContent();

                    // Should we redirect 403 errors?
                    // Should we make a difference between logged in (404) and not logged in users (redirect)?

                    if ($content !== null) {
                        // Cache the error page.
                        // To delete the cache when the content gets changed,
                        // we add the same tag as the core does.
                        if ($errorPage->useCache()) {
                            // $this->pageCache->set($cacheIdentifier, $content, ['pageId_' . $errorPage['uid']]);
                        }
                    }
                }
            }
            // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($content);exit;
            if (is_string($content)) {
                return str_replace(
                    ['###CURRENT_URL###', '###REASON###'],
                    [$error->getCurrentUrl(), $error->getReasonText()],
                    $content
                );
            }
        }

        // Fallback to core error message.
        $title = 'Page Not Found';
        $message = 'The page did not exist or was inaccessible.' . ($error->getReasonText() ? ' Reason: ' . htmlspecialchars($error->getReasonText()) : '');
        $messagePage = GeneralUtility::makeInstance(ErrorpageMessage::class, $message, $title);
        return $messagePage->render();
    }
}

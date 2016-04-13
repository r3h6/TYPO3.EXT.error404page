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
use R3H6\Error404page\Http\Request;
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
     * @var R3H6\Error404page\Domain\Repository\PageRepository
     * @inject
     */
    protected $pageRepository;

    /**
     * @var TYPO3\CMS\Core\Cache\CacheManager
     * @inject
     */
    protected $cacheManager;

    /**
     * @var TYPO3\CMS\Core\Cache\Frontend\FrontendInterface
     */
    protected $pageCache;

    /**
     * Initialize object.
     */
    public function initializeObject()
    {
        $this->pageCache = $this->cacheManager->getCache('cache_pages');
    }

    /**
     * Renders the error page.
     *
     * @param  array $params
     * @param string $host Current host
     * @param int $language System langauge uid
     * @return string Error page html.
     */
    public function handleError(array $params, $host, $language)
    {
        $currentUrl = $params['currentUrl'];
        $reason = LocalizationUtility::translate('reasonText.' . sha1($params['reasonText']), 'error404page');
        if ($reason === null) {
            $reason = $params['reasonText'];
        }

        if (!isset($_GET['tx_error404page_request'])) {
            $cacheIdentifier = sha1($host . '/' . $language);
            $content = $this->pageCache->get($cacheIdentifier);
            if ($content === false) {
                $errorPage = $this->pageRepository->findErrorPageByHost($host);
                if ($errorPage !== null) {
                    // Fallback to default language if the site has no translation.
                    $lParam = isset($errorPage['_PAGES_OVERLAY_LANGUAGE']) ? $errorPage['_PAGES_OVERLAY_LANGUAGE'] : 0;
                    $url = GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST') . '/?id=' . $errorPage['uid'] . '&L=' . $lParam . '&tx_error404page_request=' . uniqid();
                    $request = GeneralUtility::makeInstance(Request::class, $url);
                    $content = $request->send();

                    if ($content !== null) {
                        // Cache the error page.
                        // To delete the cache when the content gets changed,
                        // we add the same tag as the core does.
                        $this->pageCache->set($cacheIdentifier, $content, ['pageId_' . $errorPage['uid']]);
                    }
                }
            }
            if (is_string($content)) {
                return str_replace(
                    ['###CURRENT_URL###', '###REASON###'],
                    [$currentUrl, $reason],
                    $content
                );
            }
        }

        // Fallback to core error message.
        $title = 'Page Not Found';
        $message = 'The page did not exist or was inaccessible.' . ($reason ? ' Reason: ' . htmlspecialchars($reason) : '');
        $messagePage = GeneralUtility::makeInstance(ErrorpageMessage::class, $message, $title);
        return $messagePage->render();
    }
}

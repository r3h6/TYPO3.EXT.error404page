<?php

namespace Monogon\Page404\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use Monogon\Page404\Configuration\ExtensionConfiguration;
use Monogon\Page404\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Messaging\ErrorpageMessage;
use TYPO3\CMS\Core\Cache\CacheManager;

class ErrorPageController
{
    /**
     * @var Monogon\Page404\Domain\Repository\PageRepository
     */
    protected $pageRepository;

    /**
     * @var TYPO3\CMS\Core\Log\Logger
     */
    protected $logger;

    /**
     * @var TYPO3\CMS\Core\Cache\Frontend\FrontendInterface
     */
    protected $pageCache;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->pageRepository = GeneralUtility::makeInstance(PageRepository::class);
        $this->logger = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Log\\LogManager')->getLogger(__CLASS__);
        $this->pageCache = GeneralUtility::makeInstance(CacheManager::class)->getCache('cache_pages');
    }

    /**
     * Renders the error page.
     *
     * @param  array $params
     * @param  TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $tsfe   [description]
     * @return string Error page html.
     */
    public function handleError(array $params, \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $tsfe)
    {
        $reason = $params['reasonText'];
        $currentUrl = $params['currentUrl'];
        $host = GeneralUtility::getIndpEnv('HTTP_HOST');

        if (!isset($_GET['tx_page404_request'])) {
            $cacheIdentifier = sha1($host . '/' . $GLOBALS['TSFE']->sys_language_uid);
            $content = $this->pageCache->get($cacheIdentifier);
            if ($content === false) {
                $errorPage = $this->pageRepository->findErrorPageByHost($host);
                if ($errorPage !== null) {
                    $url = GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST') . '/?id=' . $errorPage['uid'] . '&L=' . $GLOBALS['TSFE']->sys_language_uid . '&tx_page404_request=' . uniqid();
                    $content = GeneralUtility::getUrl($url);
                    if ($content !== false) {
                        // Cache the error page.
                        // To delete the cache when the content gets changed,
                        // we add the same tag as the core does.
                        $this->pageCache->set($cacheIdentifier, $content, ['pageId_' . $errorPage['uid']]);
                    } else {
                        $content = null;
                    }
                }
            }
            if ($content !== null) {
                return str_replace(
                    ['###CURRENT_URL###', '###REASON###'],
                    [$currentUrl, $reason],
                    $content
                );
            }
        }

        $title = 'Page Not Found';
        $message = 'The page did not exist or was inaccessible.' . ($reason ? ' Reason: ' . htmlspecialchars($reason) : '');
        $messagePage = GeneralUtility::makeInstance(ErrorpageMessage::class, $message, $title);
        return $messagePage->render();
    }
}

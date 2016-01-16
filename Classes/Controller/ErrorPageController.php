<?php

namespace Monogon\Page404\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use Monogon\Page404\Configuration\ExtensionConfiguration;
use Monogon\Page404\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Messaging\ErrorpageMessage;

class ErrorPageController
{
    /**
     * TYPO3\CMS\Frontend\Page\PageRepository
     * @var Monogon\Page404\Domain\Repository\PageRepository
     */
    protected $pageRepository;

    /**
     * @var Monogon\Page404\Configuration\ExtensionConfiguration
     */
    protected $extensionConfiguration;

    public function __construct()
    {
        $this->pageRepository = GeneralUtility::makeInstance(PageRepository::class);
        $this->extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
    }

    public function showAction($params, $tsfe)
    {
        $reason = $params['reasonText'];
        $currentUrl = $params['currentUrl'];

        $host = GeneralUtility::getIndpEnv('HTTP_HOST');

        $errorPage = $this->pageRepository->findErrorPageByHost($host);
        if ($errorPage) {
            $url = GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST') . '/?id=' . $errorPage['uid'];
            $content = GeneralUtility::getUrl($url);
            if ($content !== false) {
                HttpUtility::setResponseCode(HttpUtility::HTTP_STATUS_404);
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

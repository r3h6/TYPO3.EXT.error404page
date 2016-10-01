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

use R3H6\Error404page\Domain\Model\Error;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * Error handler.
 */
class RedirectErrorHandler implements ErrorHandlerInterface
{
    /**
     * @var \R3H6\Error404page\Domain\Repository\PageRepository
     * @inject
     */
    protected $pageRepository;

    /**
     * @var \R3H6\Error404page\Configuration\PageTsConfigManager
     * @inject
     */
    protected $pageTsConfigManager;

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
     * @var \R3H6\Error404page\Service\HttpService
     * @inject
     */
    protected $httpService;

    /**
     * Redirect
     *
     * @var string
     */
    protected $redirect = '';

    /**
     * Cache tags
     *
     * @var array
     */
    protected $cacheTags = array();

    /**
     * @inherit
     */
    public function handleError(\R3H6\Error404page\Domain\Model\Error $error)
    {
        if ($error->getStatusCode() !== Error::STATUS_CODE_FORBIDDEN) {
            return false;
        }
        if (!$error->getPid()) {
            return false;
        }
        if ($this->frontendUser->isLoggedIn()) {
            return false;
        }

        /** @var \R3H6\Error404page\Configuration\PageTsConfig $pageTsConfig */
        $pageTsConfig = $this->pageTsConfigManager->getPageTsConfig($error->getPid());
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
            $this->cacheTags[] = 'pageId_' . $parameter;
            if ($this->frontendController->isDefaultType() === false) {
                $parameter .= ','.$this->frontendController->getType();
            }
            if ($this->frontendController->isDefaultLanguage() === false || $this->frontendController->isDefaultGetVar('L') === true) {
                $parameter .= ' - - - &L=' . $this->frontendController->getSystemLanguageUid();
            }
        }

        if ($parameter) {
            $this->redirect = '';
            $this->redirect .= $this->frontendController->typoLink(array('parameter' => $parameter, 'forceAbsoluteUrl' => true));

            $this->redirect .= (strpos($this->redirect, '?') === false) ? '?': '&';
            $this->redirect .= 'redirect_url=' . $error->getUrl();
            return true;
        }
        return false;
    }

    /**
     * @inherit
     */
    public function getOutput(\R3H6\Error404page\Domain\Model\Error $error)
    {
        $this->httpService->redirect($this->redirect);
    }

    /**
     * @inherit
     */
    public function setCachingData($data)
    {
        $this->redirect = (string) $data;
    }

    /**
     * @inherit
     */
    public function getCachingData()
    {
        return $this->redirect;
    }

    /**
     * @inherit
     */
    public function getCacheTags()
    {
        return $this->cacheTags;
    }

    /**
     * Get class logger
     *
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    protected function getLogger()
    {
        return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Log\\LogManager')->getLogger(__CLASS__);
    }
}

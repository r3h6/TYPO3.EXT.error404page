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

/**
 * Error handler.
 */
class Page404ErrorHandler implements ErrorHandlerInterface
{
    /**
     * @var \R3H6\Error404page\Domain\Repository\PageRepository
     * @inject
     */
    protected $pageRepository;

    /**
     * @var \R3H6\Error404page\Service\HttpService
     * @inject
     */
    protected $httpService;

    /**
     * Output.
     *
     * @var string
     */
    protected $output = '';

    /**
     * Cache tags.
     *
     * @var array
     */
    protected $cacheTags = array();

    /**
     * {@inheritdoc}
     */
    public function handleError(\R3H6\Error404page\Domain\Model\Error $error)
    {
        $errorPage = $this->pageRepository->find404PageForError($error);
        if ($errorPage !== null) {
            $this->output = $this->httpService->readUrl($errorPage->getUrl());
            if ($this->output) {
                $this->cacheTags[] = 'pageId_'.$errorPage->getUid();

                return true;
            }
        }
        $this->getLogger()->debug('Do not handle error because no error page found');

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutput(\R3H6\Error404page\Domain\Model\Error $error)
    {
        $replaceMap = array(
            '###CURRENT_URL###' => $error->getCurrentUrl(),
            '###REASON###' => $error->getReasonText(),
            '###ERROR_STATUS_CODE###' => $error->getStatusCode(),
        );

        return str_replace(array_keys($replaceMap), array_values($replaceMap), $this->output);
    }

    /**
     * {@inheritdoc}
     */
    public function setCachingData($data)
    {
        $this->output = (string) $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getCachingData()
    {
        return $this->output;
    }

    /**
     * {@inheritdoc}
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
    protected function getLogger (){
        return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Log\\LogManager')->getLogger(__CLASS__);
    }
}

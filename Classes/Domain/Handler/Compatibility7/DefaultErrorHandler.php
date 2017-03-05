<?php

namespace R3H6\Error404page\Domain\Handler\Compatibility7;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use R3H6\Error404page\Domain\Handler\ErrorHandlerInterface;

/**
 * Error handler.
 */
class DefaultErrorHandler implements ErrorHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handleError(\R3H6\Error404page\Domain\Model\Error $error)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutput(\R3H6\Error404page\Domain\Model\Error $error)
    {
        $title = 'Page not found';
        $message = 'The page did not exist or was inaccessible.'.($error->getReason() ? ' Reason: '.htmlspecialchars($error->getReason()) : '');
        /** @var \TYPO3\CMS\Core\Messaging\ErrorpageMessage $messagePage */
        $messagePage = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\ErrorpageMessage', $message, $title);

        return $messagePage->render();
    }

    /**
     * {@inheritdoc}
     */
    public function setCachingData($data)
    {
        // Only satisfy interface
    }

    /**
     * {@inheritdoc}
     */
    public function getCachingData()
    {
        return; // Only satisfy interface
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheTags()
    {
        return array();
    }
}

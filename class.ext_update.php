<?php

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

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Extension update script
 */
class ext_update
{
    /**
     * Main update function called by the extension manager.
     *
     * @return string
     */
    public function main()
    {
        $this->generateUrlHashes();
        return $this->generateOutput();
    }

    protected function generateUrlHashes()
    {
        $title = 'Generate url hashes';

        $query = 'UPDATE tx_error404page_domain_model_error SET url_hash=SHA1(url) WHERE url_hash=""';
        $result = $this->getDatabasConnection()->sql_query($query);

        if ($this->getDatabasConnection()->sql_errno()) {
            $this->messageArray[] = array(FlashMessage::ERROR, $title, 'Please update your database first through the install tool or deactivate and activate the extension once!');
            return;
        }

        $count = $this->getDatabasConnection()->sql_affected_rows($result);

        if ($count > 0) {
            $this->messageArray[] = array(FlashMessage::OK, $title, sprintf('Updated %s records!', $count));
        } else {
            $this->messageArray[] = array(FlashMessage::INFO, $title, 'Nothing to do!');
        }
    }

    /**
     * @return TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabasConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * Called by the extension manager to determine if the update menu entry
     * should by showed.
     *
     * @return bool
     * @todo find a better way to determine if update is needed or not.
     */
    public function access()
    {
        return true;
    }

    /**
     * Generates output by using flash messages
     *
     * @return string
     */
    protected function generateOutput()
    {
        $output = '';
        foreach ($this->messageArray as $messageItem) {
            /** @var \TYPO3\CMS\Core\Messaging\FlashMessage $flashMessage */
            $flashMessage = GeneralUtility::makeInstance(
                'TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
                $messageItem[2],
                $messageItem[1],
                $messageItem[0]
            );
            $output .= $flashMessage->render();
        }
        return $output;
    }
}

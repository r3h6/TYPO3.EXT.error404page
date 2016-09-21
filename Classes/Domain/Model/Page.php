<?php
namespace R3H6\Error404page\Domain\Model;

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
use R3H6\Error404page\Http\Request;

/**
 * Page
 */
class Page extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    protected $data;

    public function __construct($data)
    {
        $this->uid = (int) $data['uid'];
        $this->pid = (int) $data['pid'];
        $this->data = $data;
    }

    public function useCache()
    {
        return (false === (bool) $this->data['no_cache']);
    }

    public function getContent()
    {
        // Fallback to default language if the site has no translation.
        $lParam = isset($this->data['_PAGES_OVERLAY_LANGUAGE']) ? $this->data['_PAGES_OVERLAY_LANGUAGE'] : 0;
        $url = GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST') . '/index.php?id=' . $this->data['uid'] . '&L=' . $lParam . '&tx_error404page_request=' . uniqid() . '&return_url=' . GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL');

        return $this->fetchContent($url);
    }

    protected function fetchContent($url)
    {
        $content = GeneralUtility::getUrl($url);
        if ($content === false) {
            return null;
        }
        return $content;
    }
}

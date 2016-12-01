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
 * Page.
 */
class Page extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * Record data.
     *
     * @var array
     */
    protected $data;

    public function __construct(array $data)
    {
        $this->uid = (int) $data['uid'];
        $this->pid = (int) $data['pid'];
        $this->data = $data;
    }

    public function getTitle()
    {
        return $this->data['title'];
    }

    /**
     * Returns if page allows caching.
     *
     * @return bool
     */
    public function useCache()
    {
        return false === (bool) $this->data['no_cache'];
    }

    /**
     * Returns the TYPO3 url for the page.
     *
     * @return string
     */
    public function getUrl()
    {
        $url = GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST').'/index.php?id='.$this->data['uid'];

        // Only add additional params when they are set by the request!
        // https://github.com/r3h6/TYPO3.EXT.error404page/issues/7
        if (GeneralUtility::_GP('type') !== null) {
            $url .= '&type='.(int) GeneralUtility::_GP('type');
        }
        if (isset($this->data['_PAGES_OVERLAY_LANGUAGE'])) {
            // Fallback to default language if the site has no translation.
            $url .= '&L='.$this->data['_PAGES_OVERLAY_LANGUAGE'];
        }

        return $url;
    }
}

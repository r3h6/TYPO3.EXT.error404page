<?php

namespace R3H6\Error404page\Cache;

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
use R3H6\Error404page\Domain\Model\Error;

/**
 * PageCache
 */
class PageCache implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * @var \R3H6\Error404page\Configuration\ExtensionConfiguration
     * @inject
     */
    protected $extensionConfiguration;

    /**
     * @var \TYPO3\CMS\Core\Cache\CacheManager
     * @inject
     */
    protected $cacheManager;

    /**
     * @var \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface
     */
    protected $pageCache;

    /**
     * @var \R3H6\Error404page\Facade\FrontendUser
     * @inject
     */
    protected $frontendUser;

    /**
     * Initialize object.
     */
    public function initializeObject()
    {
        $this->pageCache = $this->cacheManager->getCache('cache_pages');
    }

    public function buildEntryIdentifierFromError(Error $error)
    {
        // if ($this->extensionConfiguration->get('enable403page') && $error->getStatusCode() === Error::STATUS_CODE_FORBIDDEN) {
        //     return sha1($error->getPid() . '/' . $error->getLanguage());
        // }
        return sha1($error->getHost() . ':' . $error->getLanguage() . ':' . $this->getFrontendUserGroups());
    }

    public function get($entryIdentifier)
    {
        return $this->pageCache->get($entryIdentifier);
    }

    public function set($entryIdentifier, $data, $pageUid, array $tags = array(), $lifetime = null)
    {
        $tags = array_merge($tags, array('pageId_' . $pageUid));
        // return $this->pageCache->set($entryIdentifier, $content, $tags);
    }
    protected function getFrontendUserGroups()
    {
        return join(',', $this->frontendUser->getUserGroups());
    }
}

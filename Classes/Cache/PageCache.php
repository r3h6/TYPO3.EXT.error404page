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
     * Initialize object.
     */
    public function initializeObject()
    {
        $this->pageCache = $this->cacheManager->getCache('cache_pages');
    }

    public function buildEntryIdentifierFromError(Error $error)
    {
        if ($this->extensionConfiguration->get('feature403') && $error->getStatusCode() === Error::STATUS_CODE_FORBIDDEN) {
            return sha1($error->getPid() . '/' . $error->getLanguage());
        }
        return sha1($error->getHost() . '/' . $error->getLanguage());
    }

    public function get($entryIdentifier)
    {
        return $this->pageCache->get($entryIdentifier);
    }

    public function set($entryIdentifier, $data, array $tags = array(), $lifetime = null)
    {
        return $this->pageCache->get($entryIdentifier, $content, $tags);
    }
}

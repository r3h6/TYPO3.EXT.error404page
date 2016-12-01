<?php

namespace R3H6\Error404page\Domain\Cache;

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
use R3H6\Error404page\Domain\Handler\ErrorHandlerInterface;

/**
 * ErrorHandlerCache.
 */
class ErrorHandlerCache implements \TYPO3\CMS\Core\SingletonInterface
{
    const IDENTIFIER = 'error404page_errorhandler';

    /**
     * Cache instance.
     *
     * @var \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface
     */
    protected $cacheInstance;

    /**
     * ObjectManager.
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     * @inject
     */
    protected $objectManager = null;

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
        // $this->cacheInstance = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager')->getCache(self::IDENTIFIER);
        $this->cacheInstance = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager')->getCache('cache_pages');
    }

    /**
     * Calculates the cache identifier for a given error.
     *
     * @param Error $error
     *
     * @return string sha1
     */
    public function calculateCacheIdentifier(Error $error)
    {
        $parts = array();
        $parts[] = $error->getHost();
        $parts[] = $error->getLanguage();
        $parts[] = $error->getStatusCode();
        $parts[] = implode(',', (array) $this->frontendUser->getUserGroups());

        if ($error->getStatusCode() === Error::STATUS_CODE_FORBIDDEN) {
            $parts[] = $error->getCurrentUrl();
        }

        return sha1(implode('---', $parts));
    }

    /**
     * Reads from cache.
     *
     * @param string $cacheIdentifier
     *
     * @return ErrorHandlerInterface|null
     */
    public function get($cacheIdentifier)
    {
        $cacheEntry = $this->cacheInstance->get($cacheIdentifier);
        if ($cacheEntry !== false) {
            $data = json_decode($cacheEntry, true);
            $errorHandler = $this->objectManager->get($data['class']);
            if ($errorHandler instanceof ErrorHandlerInterface) {
                $errorHandler->setCachingData($data['data']);

                return $errorHandler;
            }
        }

        return;
    }

    /**
     * Writes to cache.
     *
     * @param string                $cacheIdentifier
     * @param ErrorHandlerInterface $errorHandler
     */
    public function set($cacheIdentifier, ErrorHandlerInterface $errorHandler)
    {
        $tags = (array) $errorHandler->getCacheTags();

        $data = json_encode(array(
            'class' => get_class($errorHandler),
            'data' => $errorHandler->getCachingData(),
        ));

        $this->cacheInstance->set($cacheIdentifier, $data, $tags);
    }
}

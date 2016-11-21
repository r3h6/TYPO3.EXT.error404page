<?php

namespace R3H6\Error404page\Domain\Handler;

use R3H6\Error404page\Tests\Unit\Utility\RegexUtility;

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
class ErrorHandler
{
    /**
     * @var \R3H6\Error404page\Domain\Repository\ErrorRepository
     * @inject
     */
    protected $errorRepository;

    /**
     * @var \R3H6\Error404page\Configuration\ExtensionConfiguration
     * @inject
     */
    protected $extensionConfiguration;

    /**
     * @var \R3H6\Error404page\Service\HttpService
     * @inject
     */
    protected $httpService;

    /**
     * @var \R3H6\Error404page\Domain\Cache\ErrorHandlerCache
     * @inject
     */
    protected $errorHandlerCache;

    /**
     * ObjectManager
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     * @inject
     */
    protected $objectManager;

    /**
     * Output
     *
     * @var string
     */
    protected $output = '';

    /**
     * {@inheritDoc}
     */
    public function handleError(\R3H6\Error404page\Domain\Model\Error $error)
    {
        if ($this->httpService->isOwnRequest()) {
            $this->getLogger()->debug("Throw exception 1475311053");
            throw new \Exception("Error processing request", 1475311053);
        }

        if ($this->extensionConfiguration->is('enableErrorLog') && !$this->excludeFromErrorLog($error)) {
            $this->errorRepository->log($error);
        }

        $cacheIdentifier = $this->errorHandlerCache->calculateCacheIdentifier($error);
        $errorHandler = $this->errorHandlerCache->get($cacheIdentifier);
        if ($errorHandler === null) {
            foreach ($this->getErrorHandlers() as $errorHandler) {
                try {
                    $this->getLogger()->debug('Try handle error with ' . get_class($errorHandler));
                    if ($errorHandler->handleError($error)) {
                        $this->errorHandlerCache->set($cacheIdentifier, $errorHandler);
                        break;
                    }
                } catch (\Exception $exception) {
                    $this->getLogger()->debug('Could not handle error in ' . get_class($errorHandler) . ': ' . $exception->getMessage());
                }
            }
        }

        $this->getLogger()->debug('Get error handler output of ' . get_class($errorHandler));

        return $errorHandler->getOutput($error);
    }

    protected function excludeFromErrorLog(\R3H6\Error404page\Domain\Model\Error $error)
    {
        $excludePattern = trim($this->extensionConfiguration->get('excludeErrorLogPattern'));
        if (!empty($excludePattern)) {
            $regex = "/$excludePattern/i";
            if (RegexUtility::isValid($regex) && preg_match($regex, $error->getUrl())) {
                return true;
            }
        }
        return false;
    }

    protected function getErrorHandlers()
    {
        $errorHandlers = array();

        $registeredErrorHandlers = (array) $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['error404page']['errorHandlers'];
        $registeredErrorHandlers[] = 'R3H6\\Error404page\\Domain\\Handler\\RedirectErrorHandler';
        $registeredErrorHandlers[] = 'R3H6\\Error404page\\Domain\\Handler\\Page404ErrorHandler';
        $registeredErrorHandlers[] = 'R3H6\\Error404page\\Domain\\Handler\\DefaultErrorHandler';

        foreach ($registeredErrorHandlers as $className) {
            $errorHandler = $this->objectManager->get($className);
            if ($errorHandler instanceof ErrorHandlerInterface) {
                $errorHandlers[] = $errorHandler;
            }
        }

        return $errorHandlers;
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

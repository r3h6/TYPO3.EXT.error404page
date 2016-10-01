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
     * @inherit
     */
    public function handleError(\R3H6\Error404page\Domain\Model\Error $error)
    {
        if ($this->httpService->isOwnRequest()) {
            throw new \Exception("Error processing request", 1475311053);
        }

        if ($this->extensionConfiguration->is('enableErrorLog')) {
            $this->errorRepository->log($error);
        }

        $cacheIdentifier = $this->errorHandlerCache->calculateCacheIdentifier($error);
        $errorHandler = $this->errorHandlerCache->get($cacheIdentifier);
        if ($errorHandler === null) {
            foreach ($this->getErrorHandlers() as $errorHandler) {
                try {
                    if ($errorHandler->handleError($error)) {
                        // $this->errorHandlerCache->set($cacheIdentifier, $errorHandler);
                        break;
                    }
                } catch (\Exception $exception) {
                    $this->getLogger()->debug('Could not handle error in ' . get_class($errorHandler) . ': ' . $exception->getMessage());
                }
            }
        }

        return $errorHandler->getOutput($error);
    }

    protected function getErrorHandlers()
    {
        $errorHandlers = array();

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

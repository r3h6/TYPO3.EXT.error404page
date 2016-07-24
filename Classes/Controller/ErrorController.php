<?php
namespace R3H6\Error404page\Controller;

use R3H6\Error404page\Domain\Model\Dto\ErrorDemand;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;
use TYPO3\CMS\Core\Messaging\AbstractMessage;

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
 * ErrorController
 */
class ErrorController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * ErrorRepository
     *
     * @var \R3H6\Error404page\Domain\Repository\ErrorRepository
     * @inject
     */
    protected $errorRepository = null;

    /**
     * action dashboard
     *
     * @param \R3H6\Error404page\Domain\Model\Dto\ErrorDemand $demand
     * @return void
     */
    public function dashboardAction(ErrorDemand $demand = null)
    {
        if ($this->errorRepository->isConsistent() === false) {
            $this->addFlashMessage('Please execute the update script!', '', AbstractMessage::ERROR);
        }

        if ($demand === null) {
            $demand = $this->objectManager->get(ErrorDemand::class);
            $demand->setMinTime(strtotime(ErrorDemand::TIME_ONE_WEEK_AGO));
            $demand->setLimit(100);
        }

        $demand->setType(ErrorDemand::TYPE_TOP_URLS);
        $errors = $this->errorRepository->findDemanded($demand);

        $this->view->assign('errors', $errors);
        $this->view->assign('demand', $demand);
    }

    protected function initializeListAction()
    {
        $this->allowAllProperties('demand');
    }

    /**
     * action list
     *
     * @param \R3H6\Error404page\Domain\Model\Dto\ErrorDemand $demand
     * @return void
     */
    public function listAction(ErrorDemand $demand)
    {
        $errors = $this->errorRepository->findDemanded($demand);
        $this->view->assign('errors', $errors);
        $this->view->assign('demand', $demand);
    }

    protected function initializeShowAction()
    {
        $this->allowAllProperties('demand');
    }

    /**
     * action show
     *
     * @param \R3H6\Error404page\Domain\Model\Dto\ErrorDemand $demand
     * @return void
     */
    public function showAction(ErrorDemand $demand)
    {
        $this->view->assign('error', $this->errorRepository->findOneByUrlHash($demand->getUrlHash()));
        $this->view->assign('demand', $demand);
    }

    /**
     * action deleteAll
     *
     * @return void
     */
    public function deleteAllAction()
    {
        $this->errorRepository->deleteAll();
        $this->addFlashMessage('Truncated errors log.');
        $this->redirect('dashboard');
    }

    protected function allowAllProperties($argument)
    {
        $propertyMappingConfiguration = $this->arguments->getArgument($argument)->getPropertyMappingConfiguration();
        $propertyMappingConfiguration->allowAllProperties();
        $propertyMappingConfiguration->setTypeConverterOption(PersistentObjectConverter::class, PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED, true);
    }
}

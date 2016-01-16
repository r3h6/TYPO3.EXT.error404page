<?php

namespace Monogon\Page404\Configuration;

class ExtensionConfiguration implements \TYPO3\CMS\Core\SingletonInterface
{
    public function getErrorPageType()
    {
        return 104;
    }
}

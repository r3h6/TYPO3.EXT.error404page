<?php

namespace Monogon\Page404\Domain\Model;

class Page
{
    protected $uid;

    public function __construct($uid)
    {
        $this->uid = $uid;
    }
}

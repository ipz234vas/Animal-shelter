<?php

namespace classes;

abstract class BaseController
{
    public ModelState $modelState;

    public function __construct()
    {
        $this->modelState = new ModelState();
    }
}
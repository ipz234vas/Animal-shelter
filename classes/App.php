<?php

namespace classes;

class App
{
    public function run(): void
    {
        $core = Core::getInstance();
        $core->init();
        $core->run();
        $core->done();
    }
}
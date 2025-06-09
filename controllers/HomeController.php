<?php

namespace controllers;

use classes\Template;

class HomeController
{
    public function addAction()
    {
        $tpl = new Template('views/home/add.php');
        return [
            'Title' => "Головна сторінка",
            'Content' => $tpl->render()
        ];
    }
}
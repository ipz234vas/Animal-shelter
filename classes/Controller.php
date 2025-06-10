<?php

namespace classes;

class Controller
{
    public Template $template;

    function __construct()
    {
        $module = Core::getInstance()->module;
        $action = Core::getInstance()->action;
        $this->template = new Template("views/{$module}/{$action}.php");
    }

    function view(array $params = []): array
    {
        if (!empty($params)) {
            $this->template->addParams($params);
        }
        return [
            'Content' => $this->template->render()
        ];
    }
}
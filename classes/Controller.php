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

    function view(string $title, array $params = []): array
    {
        if (!empty($params)) {
            $this->template->addParams($params);
        }
        return [
            'Title' => $title,
            'Content' => $this->template->render()
        ];
    }
}
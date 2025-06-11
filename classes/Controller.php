<?php

namespace classes;

class Controller extends BaseController
{
    public Template $template;

    function __construct()
    {
        $module = Core::getInstance()->module;
        $action = Core::getInstance()->action;
        $this->template = new Template("views/{$module}/{$action}.php");
        parent::__construct();
    }

    function view(array $params = [], string $path = ''): array
    {
        if ($path) {
            $this->template = new Template($path);
        }
        if (!empty($params)) {
            $this->template->addParams($params);
        }
        return [
            'Content' => $this->template->render()
        ];
    }

    function redirect(string $controller = '', string $action = '', array $params = []): never
    {
        $path = '/';

        if (!empty($controller)) {
            $path .= $controller;
            if (!empty($action)) {
                $path .= '/' . $action;
            }
        }

        if (!empty($params)) {
            $path .= '?' . http_build_query($params);
        }
        $this->redirectToPath($path);
    }

    function redirectToPath($path): never
    {
        header("Location: $path");
        exit;
    }
}
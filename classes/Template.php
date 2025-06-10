<?php

namespace classes;

class Template
{
    protected array $params;
    protected string $template_filepath;

    public function __construct(string $filepath)
    {
        $this->params = [];
        $this->template_filepath = $filepath;
    }

    public function __set(string $name, $value)
    {
        Core::getInstance()->template->addParam($name, $value);
    }

    public function setTemplateFilepath(string $filepath): void
    {
        $this->template_filepath = $filepath;
    }

    public function addParam(string $name, string $value): void
    {
        $this->params[$name] = $value;
    }

    public function addParams(array $params): void
    {
        $this->params = array_merge($this->params, $params);
    }

    public function render(): string
    {
        extract($this->params);
        ob_start();
        include $this->template_filepath;
        $str = ob_get_contents();
        ob_end_clean();
        return $str;
    }

    public function display(): void
    {
        echo $this->render();
    }
}
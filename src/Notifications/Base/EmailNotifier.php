<?php

namespace App\Notifications\Base;

use App\Interfaces\Notifications\INotifier;
use Exception;

abstract class EmailNotifier implements INotifier
{
    protected string $templatePath;

    public function __construct(string $templatePath = __DIR__. '/../templates/'){
        $this->templatePath = rtrim($templatePath, "/") . "/";
    }

    protected function renderTemplate(string $fileName, array $variables):string
    {
        $file = $this->templatePath.$fileName;
        if(!file_exists($file)){
            throw new Exception("Template não encontrado: $file");
        }

        $template = file_get_contents($file);
        foreach($variables as $key => $value){
            $template = str_replace("{{{$key}}}", htmlspecialchars($value), $template);
        }

        return $template;
    }

    abstract protected function send(string $to, string $subject, string $body):bool | array;
}
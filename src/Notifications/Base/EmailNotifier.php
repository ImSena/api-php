<?php

namespace App\Notifications\Base;

use App\Interfaces\Notifications\INotifier;
use Exception;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

abstract class EmailNotifier implements INotifier
{
    protected Environment $twig;
    protected ?array $var_default_email;

    public function __construct(?array $var_default_email = null, string $templatePath = __DIR__ . '/../templates/')
    {

        $loader = new FilesystemLoader(rtrim($templatePath, "/"));

        $this->twig = new Environment($loader, [
            'cache' => false
        ]);

        $this->var_default_email = $var_default_email;
    }

    protected function renderTemplate(string $fileName, array $variables, string $type = 'USER'): string
    {
        try {
            if($type == "ADMIN"){
                $fileWithPath = "admin/".$fileName;
                return $this->twig->render($fileWithPath, $variables);
            }

            return $this->twig->render($fileName, $variables);
        } catch (Exception $e) {
            throw new Exception("Erro ao renderizar template: {$fileName} — {$e->getMessage()}");
        }
    }

    abstract protected function send(string $to, string $subject, string $body, ?array $attachments = null): bool | array;
}

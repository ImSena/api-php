<?php

namespace App\Controllers;
define('ROOT_PATH', realpath(__DIR__ .'/../..'));

// No UserController.php:
require_once ROOT_PATH . '/config.php';

class UserController
{
    public function teste()
    {
        // Caminhos
        
        $inputPath = PHOTO . 'teste.png';
        $outputPath = PHOTO . 'teste.png';
        // Executa o script Python
        $command = escapeshellcmd("python3 ".TOOLS."removeBg.py $inputPath $outputPath");
        $output = shell_exec($command);

        // Retorna o resultado
        if (file_exists($outputPath)) {
            echo "Imagem processada com sucesso: <a href='$outputPath'>Download</a>";
        } else {
            echo "Erro ao processar a imagem.";
        }
    }
}

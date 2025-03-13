<?php

namespace App\Utils;

use Exception;

class ValidatorFiles
{
    public static function validate(array $files): array
    {
        $errors = [];

        $allowedTypes = [
            'image/jpg',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
            'video/mp4',
            'video/quicktime',
            'video/x-msvideo',
            'video/x-matroska',
            'video/webm',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/csv',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'audio/mpeg',
            'audio/wav',
            'audio/ogg',
            'audio/aac',
            'image/x-icon',
        ];

        $allowedExtensions = [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'webp',
            'svg',
            'mp4',
            'mov',
            'avi',
            'mkv',
            'webm',
            'pdf',
            'doc',
            'docx',
            'txt',
            'rtf',
            'odt',
            'csv',
            'xls',
            'xlsx',
            'ods',
            'ppt',
            'pptx',
            'odp',
            'mp3',
            'wav',
            'ogg',
            'aac',
            'ico'
        ];

        $maxSize = 50 * 1024 * 1024;

        foreach ($files as $index => $value) {

            if (empty($value)) {
                $errors[] = "É necessário ter um arquivo para ser enviado";
                break;
            }

            foreach ($value as $file) {
                if (!in_array($file['type'], $allowedTypes)) {
                    $errors[] = "Tipo de arquivo não permitido para o arquivo {$file['name']}";
                    break;
                }

                if ($file['size'] > $maxSize) {
                    $errors[] = "O arquivo {$file['name']} é muito grande. Tamanho máximo é de 50Kb";
                    break;
                }

                if ($file['error'] !== UPLOAD_ERR_OK) {
                    $errors[] = "Erro no upload do arquivo {$file['name']}.";
                    break;
                }

                $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

                if (!in_array($extension, $allowedExtensions)) {
                    $errors[] = "A extensão do arquivo {$file['name']} não é permitida";
                    break;
                }

                if (strpos($file['type'], 'image') === 0) {
                    if (getimagesize($file['tmp_name']) === false) {
                        $errors[] = "O arquivo {$file['name']} não é uma imagem válida.";
                    }

                    list($width, $height) = getimagesize($file['tmp_name']);

                    if ($width < 100 || $height < 100) {
                        $errors[] = "A imagem {$file['name']} deve ter pelo menos 100x100 pixels.";
                        break;
                    }
                }
            }
        }

        if (!empty($errors)) {
            throw new Exception("Erros de validação: " . implode(', ', $errors));
        }

        return $files;
    }
}

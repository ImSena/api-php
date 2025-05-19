<?php

namespace App\Utils;

class FormatterLinkProduct
{
    public static function formatter(string $url):string
    {
        $url = mb_strtolower($url, 'UTF-8');

        $url = iconv('UTF-8', 'ASCII//TRANSLIT', $url);
        $url = preg_replace('/[^a-z0-9\s-]/', '', $url);

        $url = preg_replace('/\s+/', '-', $url);

        $url = preg_replace('/-+/', '-', $url);

        $url = trim($url, '-');

        return $url;
    }
}

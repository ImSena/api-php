<?php

namespace App\Interfaces;

use PDO;

interface IModel{
    public function getPdo(): PDO;
}
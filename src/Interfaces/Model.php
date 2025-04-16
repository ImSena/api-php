<?php

namespace App\Interfaces;

use PDO;

interface Model{
    public function getPdo(): PDO;
}
<?php

namespace App\Controllers;

use App\Controllers\Base\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        header("Location: documentation");
    }
}
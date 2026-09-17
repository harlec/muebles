<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Local;

class DashboardController extends Controller
{
    public function index(): void
    {
        $config = require dirname(__DIR__, 2) . '/config/config.php';

        $this->view('dashboard/index', [
            'locales'     => Local::all(),
            'currency'    => $config['app']['currency'],
            'layoutWidth' => $config['app']['layout_width'],
        ]);
    }
}

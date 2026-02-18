<?php

namespace App\Http\Controllers;
use App\Http\Controllers\DashboardController;

use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $alertas = app(DashboardController::class)->getMantenimientoAlertas();
        return view('admin.dashboard', compact('alertas'));
    }
}

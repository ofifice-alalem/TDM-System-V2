<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Shared\MainStockController;
use Illuminate\Http\Request;

class AdminMainStockController extends Controller
{
    public function __construct(private MainStockController $mainStockController)
    {
    }

    public function index(Request $request)
    {
        return $this->mainStockController->index($request);
    }
}

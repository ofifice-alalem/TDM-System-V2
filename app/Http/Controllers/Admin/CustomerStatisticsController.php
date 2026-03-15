<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Sales\StatisticsController;

class CustomerStatisticsController extends StatisticsController
{
    protected function viewPrefix(): string { return 'admin.customer-statistics'; }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MonitoringService;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function __construct(protected MonitoringService $monitoringService) {}

    public function index()
    {
        return inertia('admin/monitoring/index', [
            'monitorings' => $this->monitoringService->index(),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\CustomClasses\Column;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class MonitoringController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $query = Activity::query()
            ->with('causer', 'subject')
            ->latest();

        $tableData = \App\CustomClasses\TableData::make(
            $query,
            [
                Column::make('created_at', 'timestamp')
                    ->setView('reusable-table.column.date-yyyy'),
                Column::make('causer', 'user')
                    ->setView('reusable-table.column.user-card'),
                Column::make('event', 'action'),
                Column::make('description', 'details')
            ],
            perPage: request('perPage', 10),
            id: 'log-activity-table',
        );

        $dailyActiveUsers = Activity::query()
            ->where('event', 'login')
            ->whereDate('created_at', now())
            ->distinct('causer_id')
            ->count();


        return view('pages.admin.monitoring.index', compact('tableData', 'dailyActiveUsers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

<?php

namespace App\Services;

use App\Facades\DataTable;
use App\Http\Resources\ContentStudentResourc;
use App\Models\ContentStudent;

class MonitoringService
{
    public function index()
    {
        $query = ContentStudent::query();

        $result = DataTable::query($query)
            ->with(['content', 'user', 'content.contentable', 'content.classroom'])
            ->searchable(['user.name', 'content.classroom.name'])
            ->make();

        return ContentStudentResourc::collection($result);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;

class NotificationLogController extends Controller
{
    public function index()
    {
        return view('admin.notifications.index', [
            'logs' => NotificationLog::query()->latest()->paginate(30),
        ]);
    }
}

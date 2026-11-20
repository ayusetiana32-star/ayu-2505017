<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $now = now();
        $unfinishedTasks = fn () => $user->tasks()->where('status', '!=', 'selesai');

        $stats = [
            'unfinished' => $unfinishedTasks()->count(),
            'overdue' => $unfinishedTasks()->where('deadline', '<', $now)->count(),
            'today' => $unfinishedTasks()
                ->whereBetween('deadline', [$now, $now->copy()->endOfDay()])
                ->count(),
            'next_seven_days' => $unfinishedTasks()
                ->whereBetween('deadline', [
                    $now->copy()->addDay()->startOfDay(),
                    $now->copy()->addDays(7)->endOfDay(),
                ])
                ->count(),
        ];

        $deadlineTasks = $unfinishedTasks()
            ->with('course')
            ->where('deadline', '<=', $now->copy()->addDays(7)->endOfDay())
            ->orderBy('deadline')
            ->limit(8)
            ->get();

        return view('dashboard', compact('stats', 'deadlineTasks'));
    }
}

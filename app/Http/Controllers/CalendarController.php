<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StaffLeave;
use App\Holiday;
use App\LeaveType;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the calendar view.
     */
    public function index()
    {
        $leaveTypes = LeaveType::all();
        return view('calendar', compact('leaveTypes'));
    }

    /**
     * Get calendar events as JSON for AJAX.
     */
    public function events(Request $request)
    {
        $start = $request->input('start', Carbon::now()->startOfMonth()->toDateString());
        $end = $request->input('end', Carbon::now()->endOfMonth()->toDateString());

        $events = [];

        // Get approved leaves
        $leaves = StaffLeave::with(['staff', 'leaveType'])
            ->whereHas('leaveAction', function ($q) {
                $q->where('status_id', 2); // Approved
            })
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($q2) use ($start, $end) {
                        $q2->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                    });
            })
            ->get();

        foreach ($leaves as $leave) {
            $events[] = [
                'id' => 'leave-' . $leave->id,
                'title' => ($leave->staff->firstname ?? 'Unknown') . ' - ' . ($leave->leaveType->leave_type_name ?? 'Leave'),
                'start' => $leave->start_date,
                'end' => Carbon::parse($leave->end_date)->addDay()->toDateString(), // FullCalendar end is exclusive
                'color' => $this->getLeaveTypeColor($leave->leave_type_id),
                'type' => 'leave',
            ];
        }

        // Get holidays
        $holidays = Holiday::whereBetween('date', [$start, $end])->get();

        foreach ($holidays as $holiday) {
            $events[] = [
                'id' => 'holiday-' . $holiday->id,
                'title' => '🎉 Public Holiday',
                'start' => $holiday->date,
                'end' => $holiday->date,
                'color' => '#4caf50',
                'type' => 'holiday',
                'allDay' => true,
            ];
        }

        return response()->json($events);
    }

    /**
     * Get color for leave type.
     */
    private function getLeaveTypeColor($leaveTypeId)
    {
        $colors = [
            1 => '#2196F3', // Annual Leave - Blue
            2 => '#ff9800', // Sick Leave - Orange
            3 => '#e91e63', // Maternity - Pink
            4 => '#9c27b0', // Paternity - Purple
            5 => '#00bcd4', // Examinations - Cyan
            6 => '#795548', // Sports - Brown
        ];

        return $colors[$leaveTypeId] ?? '#607d8b';
    }
}

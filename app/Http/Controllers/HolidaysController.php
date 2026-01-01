<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidaysController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of holidays.
     */
    public function index()
    {
        $holidays = Holiday::orderBy('date', 'desc')->get();
        return view('admin.holidays.index', compact('holidays'));
    }

    /**
     * Show the form for creating a new holiday.
     */
    public function create()
    {
        return view('admin.holidays.create');
    }

    /**
     * Store a newly created holiday.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'date' => 'required|date|unique:holidays,date',
        ]);

        Holiday::create($validated);

        return redirect()->route('holidays.index')->with('success', 'Holiday created successfully!');
    }

    /**
     * Show the form for editing.
     */
    public function edit($id)
    {
        $holiday = Holiday::findOrFail($id);
        return view('admin.holidays.edit', compact('holiday'));
    }

    /**
     * Update the specified holiday.
     */
    public function update(Request $request, $id)
    {
        $holiday = Holiday::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'date' => 'required|date|unique:holidays,date,' . $id,
        ]);

        $holiday->update($validated);

        return redirect()->route('holidays.index')->with('success', 'Holiday updated successfully!');
    }

    /**
     * Remove the specified holiday.
     */
    public function destroy($id)
    {
        $holiday = Holiday::findOrFail($id);
        $holiday->delete();

        return redirect()->route('holidays.index')->with('success', 'Holiday deleted successfully!');
    }
}

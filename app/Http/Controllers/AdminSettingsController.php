<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SettingsService;
use App\Models\SystemSetting;

class AdminSettingsController extends Controller
{
    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->middleware(['auth', 'superadmin']);
        $this->settingsService = $settingsService;
    }

    /**
     * Display the settings management page
     */
    public function index()
    {
        $settings = $this->settingsService->getAllGrouped();

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update multiple settings at once
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.id' => 'required|exists:system_settings,id',
            'settings.*.value' => 'nullable',
        ]);

        foreach ($validated['settings'] as $settingData) {
            $setting = SystemSetting::find($settingData['id']);
            $setting->setCastedValue($settingData['value'] ?? '');
            $setting->save();
        }

        // Clear cache
        $this->settingsService->clearCache();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully!',
            ]);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully!');
    }

    /**
     * Create a new setting (for advanced users)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:system_settings,key|max:255',
            'value' => 'required',
            'type' => 'required|in:string,integer,boolean,json,text',
            'group' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $setting = new SystemSetting();
        $setting->key = $validated['key'];
        $setting->type = $validated['type'];
        $setting->group = $validated['group'];
        $setting->label = $validated['label'];
        $setting->description = $validated['description'] ?? null;
        $setting->setCastedValue($validated['value']);
        $setting->save();

        $this->settingsService->clearCache();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Setting created successfully!',
                'setting' => $setting,
            ]);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Setting created successfully!');
    }

    /**
     * Delete a setting (for advanced users)
     */
    public function destroy($id)
    {
        $setting = SystemSetting::findOrFail($id);

        // Prevent deletion of critical settings
        $protectedKeys = [
            'system.app_name',
            'system.timezone',
            'leave.default_annual_days',
        ];

        if (in_array($setting->key, $protectedKeys)) {
            return response()->json([
                'success' => false,
                'message' => 'This setting cannot be deleted as it is required for system operation.',
            ], 403);
        }

        $setting->delete();
        $this->settingsService->clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Setting deleted successfully!',
        ]);
    }
}

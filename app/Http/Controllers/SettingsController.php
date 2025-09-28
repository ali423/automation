<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->shareView();
    }

    /**
     * Display a listing of the settings.
     */
    public function index()
    {
        $settings = Setting::orderBy('name')->get();
        return view('dashboard.settings.index', compact('settings'));
    }

    /**
     * Show the form for creating a new setting.
     */
    public function create()
    {
        return view('dashboard.settings.create');
    }

    /**
     * Store a newly created setting in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|unique:settings,key',
            'name' => 'required|string|max:255',
            'value' => 'required',
            'type' => 'required|in:string,number,boolean,json',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Set new settings to active by default
        $data = $request->all();
        $data['is_active'] = true;
        
        Setting::create($data);

        return redirect()->route('settings.index')
            ->with('success', 'تنظیمات با موفقیت ایجاد شد.');
    }

    /**
     * Display the specified setting.
     */
    public function show(Setting $setting)
    {
        return view('dashboard.settings.show', compact('setting'));
    }

    /**
     * Show the form for editing the specified setting.
     */
    public function edit(Setting $setting)
    {
        return view('dashboard.settings.edit', compact('setting'));
    }

    /**
     * Update the specified setting in storage.
     */
    public function update(Request $request, Setting $setting)
    {
        // Prevent changing critical setting keys
        $criticalKeys = ['vat_rate'];
        if (in_array($setting->key, $criticalKeys) && $request->input('key') !== $setting->key) {
            return redirect()->back()
                ->withErrors(['key' => 'تغییر کلید تنظیمات حیاتی مجاز نیست. این تنظیمات برای عملکرد صحیح سیستم ضروری هستند.'])
                ->withInput();
        }

        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|unique:settings,key,' . $setting->id,
            'name' => 'required|string|max:255',
            'value' => 'required',
            'type' => 'required|in:string,number,boolean,json',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Remove is_active from form data since it's not in the edit form
        $data = $request->except(['is_active']);
        
        $setting->update($data);

        // Clear cache for this setting
        Setting::clearCache();

        return redirect()->route('settings.index')
            ->with('success', 'تنظیمات با موفقیت به‌روزرسانی شد.');
    }


    /**
     * Toggle setting active status
     */
    public function toggle(Setting $setting)
    {
        $setting->update(['is_active' => !$setting->is_active]);
        
        // Clear cache
        Setting::clearCache();

        return redirect()->back()
            ->with('success', 'وضعیت تنظیمات تغییر کرد.');
    }
}

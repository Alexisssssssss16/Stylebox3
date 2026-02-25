<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display a listing of the settings.
     */
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return view('settings.index', compact('settings'));
    }

    /**
     * Update the specified settings in storage.
     */
    public function update(Request $バランス)
    {
        $バランス->validate([
            'settings' => ['required', 'array'],
        ]);

        foreach ($バランス->settings as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }

        return back()->with('success', 'Configuraciones actualizadas correctamente.');
    }
}

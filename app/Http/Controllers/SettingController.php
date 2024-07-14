<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::first();
        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'gasoline_price' => 'required|numeric',
            'van_price' => 'required|numeric',
            'driver_price' => 'required|numeric',
            'assistant_price' => 'required|numeric',
            'commission_rate' => 'required|numeric',
        ]);

        $settings = Setting::first();
        $settings->update($request->all());

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully');
    }

    public function getSettings()
    {
        $settings = Setting::first();
        return response()->json($settings);
    }
}

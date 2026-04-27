<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use Illuminate\Http\Request;

class PlatformSettingController extends Controller
{
    public function edit()
    {
        $settings = PlatformSetting::current();
        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = PlatformSetting::current();
        
        $settings->update([
            'allow_template_creation' => $request->has('allow_template_creation'),
            'allow_card_forging' => $request->has('allow_card_forging'),
            'allow_battle_creation' => $request->has('allow_battle_creation'),
        ]);

        return redirect()->route('admin.settings.edit')
                         ->with('success', 'Platform settings updated successfully.');
    }
}

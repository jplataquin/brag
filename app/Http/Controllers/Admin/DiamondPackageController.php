<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiamondPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Str;

class DiamondPackageController extends Controller
{
    public function index()
    {
        $packages = DiamondPackage::orderBy('diamonds', 'asc')->get();
        return view('admin.diamond_packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.diamond_packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'diamonds' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'promo_price' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:10',
            'qr_code' => 'nullable|image|max:2048',
            'temporary_qr_path' => 'nullable|string',
            'is_active' => 'boolean',
            'allow_manual' => 'boolean',
            'allow_hitpay' => 'boolean',
        ]);

        $packageData = $request->except(['qr_code', 'temporary_qr_path']);
        $packageData['is_active'] = $request->has('is_active');
        $packageData['allow_manual'] = $request->has('allow_manual');
        $packageData['allow_hitpay'] = $request->has('allow_hitpay');

        // Handle chunked upload
        if ($request->filled('temporary_qr_path')) {
            $tempPath = $request->input('temporary_qr_path');
            if (strpos($tempPath, 'tmp/uploads/') === 0 && Storage::disk('public')->exists($tempPath)) {
                $extension = pathinfo($tempPath, PATHINFO_EXTENSION);
                $filename = 'qr_' . time() . '_' . Str::random(10) . '.' . $extension;
                $finalPath = 'qr/' . $filename;
                Storage::disk('public')->move($tempPath, $finalPath);
                $packageData['qr_path'] = $finalPath;
            }
        } 
        // Fallback for direct upload
        elseif ($request->hasFile('qr_code')) {
            $path = $request->file('qr_code')->store('qr', 'public');
            $packageData['qr_path'] = $path;
        }

        DiamondPackage::create($packageData);

        return redirect()->route('admin.diamond-packages.index')->with('success', 'Diamond package created successfully.');
    }

    public function edit(DiamondPackage $diamondPackage)
    {
        return view('admin.diamond_packages.edit', compact('diamondPackage'));
    }

    public function update(Request $request, DiamondPackage $diamondPackage)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'diamonds' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'promo_price' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:10',
            'qr_code' => 'nullable|image|max:2048',
            'temporary_qr_path' => 'nullable|string',
            'is_active' => 'boolean',
            'allow_manual' => 'boolean',
            'allow_hitpay' => 'boolean',
        ]);

        $packageData = $request->except(['qr_code', 'temporary_qr_path']);
        $packageData['is_active'] = $request->has('is_active');
        $packageData['allow_manual'] = $request->has('allow_manual');
        $packageData['allow_hitpay'] = $request->has('allow_hitpay');

        // Handle chunked upload
        if ($request->filled('temporary_qr_path')) {
            $tempPath = $request->input('temporary_qr_path');
            if (strpos($tempPath, 'tmp/uploads/') === 0 && Storage::disk('public')->exists($tempPath)) {
                // Delete old QR code
                if ($diamondPackage->qr_path) {
                    Storage::disk('public')->delete($diamondPackage->qr_path);
                }
                
                $extension = pathinfo($tempPath, PATHINFO_EXTENSION);
                $filename = 'qr_' . time() . '_' . Str::random(10) . '.' . $extension;
                $finalPath = 'qr/' . $filename;
                Storage::disk('public')->move($tempPath, $finalPath);
                $packageData['qr_path'] = $finalPath;
            }
        }
        // Fallback for direct upload
        elseif ($request->hasFile('qr_code')) {
            // Delete old QR code if exists
            if ($diamondPackage->qr_path) {
                Storage::disk('public')->delete($diamondPackage->qr_path);
            }
            $path = $request->file('qr_code')->store('qr', 'public');
            $packageData['qr_path'] = $path;
        }

        $diamondPackage->update($packageData);

        return redirect()->route('admin.diamond-packages.index')->with('success', 'Diamond package updated successfully.');
    }

    public function destroy(DiamondPackage $diamondPackage)
    {
        if ($diamondPackage->qr_path) {
            Storage::disk('public')->delete($diamondPackage->qr_path);
        }
        $diamondPackage->delete();
        return redirect()->route('admin.diamond-packages.index')->with('success', 'Diamond package deleted successfully.');
    }
}

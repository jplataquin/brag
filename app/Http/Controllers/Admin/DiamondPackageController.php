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
            'ocr_match_string' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'allow_manual' => 'boolean',
            'allow_hitpay' => 'boolean',
        ]);

        $packageData = $request->except(['qr_code', 'temporary_qr_path']);
        $packageData['is_active'] = $request->has('is_active');
        $packageData['allow_manual'] = $request->has('allow_manual');
        $packageData['allow_hitpay'] = $request->has('allow_hitpay');

        $finalPath = null;
        $tempFileToClean = null;

        // Handle chunked upload
        if ($request->filled('temporary_qr_path')) {
            $tempPath = $request->input('temporary_qr_path');
            if (strpos($tempPath, 'tmp/uploads/') === 0 && Storage::disk('public')->exists($tempPath)) {
                $finalPath = 'qr/qr_' . time() . '_' . Str::random(10) . '.jpg';
                if ($this->convertToJpeg(Storage::disk('public')->path($tempPath), Storage::disk('public')->path($finalPath))) {
                    $packageData['qr_path'] = $finalPath;
                    $tempFileToClean = $tempPath;
                }
            }
        } 
        // Fallback for direct upload
        elseif ($request->hasFile('qr_code')) {
            $tempPath = $request->file('qr_code')->store('tmp', 'public');
            $finalPath = 'qr/qr_' . time() . '_' . Str::random(10) . '.jpg';
            if ($this->convertToJpeg(Storage::disk('public')->path($tempPath), Storage::disk('public')->path($finalPath))) {
                $packageData['qr_path'] = $finalPath;
                $tempFileToClean = $tempPath;
            }
        }

        DiamondPackage::create($packageData);

        // Cleanup temp file if conversion was successful
        if ($tempFileToClean) {
            Storage::disk('public')->delete($tempFileToClean);
        }

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
            'ocr_match_string' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'allow_manual' => 'boolean',
            'allow_hitpay' => 'boolean',
        ]);

        $packageData = $request->except(['qr_code', 'temporary_qr_path']);
        $packageData['is_active'] = $request->has('is_active');
        $packageData['allow_manual'] = $request->has('allow_manual');
        $packageData['allow_hitpay'] = $request->has('allow_hitpay');

        $tempFileToClean = null;
        $newQrPath = null;

        // Handle chunked upload
        if ($request->filled('temporary_qr_path')) {
            $tempPath = $request->input('temporary_qr_path');
            if (strpos($tempPath, 'tmp/uploads/') === 0 && Storage::disk('public')->exists($tempPath)) {
                $newQrPath = 'qr/qr_' . time() . '_' . Str::random(10) . '.jpg';
                if ($this->convertToJpeg(Storage::disk('public')->path($tempPath), Storage::disk('public')->path($newQrPath))) {
                    $packageData['qr_path'] = $newQrPath;
                    $tempFileToClean = $tempPath;
                }
            }
        }
        // Fallback for direct upload
        elseif ($request->hasFile('qr_code')) {
            $tempPath = $request->file('qr_code')->store('tmp', 'public');
            $newQrPath = 'qr/qr_' . time() . '_' . Str::random(10) . '.jpg';
            if ($this->convertToJpeg(Storage::disk('public')->path($tempPath), Storage::disk('public')->path($newQrPath))) {
                $packageData['qr_path'] = $newQrPath;
                $tempFileToClean = $tempPath;
            }
        }

        // If a new QR was successfully converted, delete the old one
        if ($newQrPath && $diamondPackage->qr_path) {
            Storage::disk('public')->delete($diamondPackage->qr_path);
        }

        $diamondPackage->update($packageData);

        // Cleanup temp file
        if ($tempFileToClean) {
            Storage::disk('public')->delete($tempFileToClean);
        }

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

    /**
     * Convert an image to JPEG with a white background.
     */
    private function convertToJpeg($sourcePath, $destPath)
    {
        $info = getimagesize($sourcePath);
        if (!$info) return false;

        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($sourcePath);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($sourcePath);
                break;
            default:
                return false;
        }

        if (!$image) return false;

        // Create a new true color image with the same dimensions
        $width = imagesx($image);
        $height = imagesy($image);
        $finalImage = imagecreatetruecolor($width, $height);

        // Fill with white background (important for transparent PNG/WEBP)
        $white = imagecolorallocate($finalImage, 255, 255, 255);
        imagefill($finalImage, 0, 0, $white);

        // Copy the source image onto the white background
        imagecopy($finalImage, $image, 0, 0, 0, 0, $width, $height);

        // Save as JPEG
        $success = imagejpeg($finalImage, $destPath, 90); // 90 quality

        // Free memory
        imagedestroy($image);
        imagedestroy($finalImage);

        return $success;
    }
}

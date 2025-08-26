<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Hotel;
use App\Models\Prefecture;
use App\Http\Requests\CreateHotelRequest;
use App\Http\Requests\EditHotelRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HotelController extends Controller
{
    /** get methods */

    public function showSearch(): View
    {
        return view('admin.hotel.search');
    }

    public function showResult(): View
    {
        return view('admin.hotel.result');
    }

    public function showEdit(Request $request): View
    {
        $hotelId = $request->query('hotel_id');

        if (!$hotelId) {
            abort(404, 'ホテルIDが指定されていません。');
        }

        $hotel = Hotel::with('prefecture')->findOrFail($hotelId);
        $prefectures = Prefecture::getAllPrefectures();

        return view('admin.hotel.edit', compact('hotel', 'prefectures'));
    }

    public function showCreate(): View
    {
        $prefectures = Prefecture::getAllPrefectures();
        return view('admin.hotel.create', compact('prefectures'));
    }

    /** post methods */

    public function searchResult(Request $request): View
    {
        $var = [];
        $hotelNameToSearch = $request->input('hotel_name');
        $hotelList = Hotel::getHotelListByName($hotelNameToSearch);

        $var['hotelList'] = $hotelList;

        return view('admin.hotel.result', $var);
    }

    public function edit(EditHotelRequest $request): RedirectResponse
    {
        return DB::transaction(function () use ($request) {
            try {
                $validatedData = $request->validated();
                $hotelId = $validatedData['hotel_id'];

                // Get the current hotel
                $hotel = Hotel::findOrFail($hotelId);
                $currentFilePath = $hotel->file_path;
                $newFilePath = $currentFilePath;

                // Handle new file upload
                if ($request->hasFile('hotel_image')) {
                    $file = $request->file('hotel_image');
                    $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $newFilePath = $file->storeAs('hotels', $fileName, 'public');

                    // Delete old file if exists and not already deleted
                    if ($currentFilePath && $currentFilePath !== $newFilePath && Storage::disk('public')->exists($currentFilePath)) {
                        Storage::disk('public')->delete($currentFilePath);
                    }
                }

                // Update hotel
                $hotelData = [
                    'hotel_name' => $validatedData['hotel_name'],
                    'prefecture_id' => $validatedData['prefecture_id'],
                    'file_path' => $newFilePath,
                ];

                $updatedHotel = Hotel::updateHotel($hotelId, $hotelData);

                return redirect()
                    ->route('adminHotelEditPage', ['hotel_id' => $hotelId])
                    ->with('success', 'ホテル「' . $updatedHotel->hotel_name . '」を正常に更新しました。');
            } catch (\Exception $e) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['error' => 'ホテルの更新中にエラーが発生しました。もう一度お試しください。']);
            }
        });
    }

    public function create(CreateHotelRequest $request): RedirectResponse
    {
        return DB::transaction(function () use ($request) {
            try {
                $validatedData = $request->validated();

                // Handle file upload if present
                $filePath = null;
                if ($request->hasFile('hotel_image')) {
                    $file = $request->file('hotel_image');
                    $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('hotels', $fileName, 'public');
                }

                // Create hotel
                $hotelData = [
                    'hotel_name' => $validatedData['hotel_name'],
                    'prefecture_id' => $validatedData['prefecture_id'],
                    'file_path' => $filePath,
                ];

                $hotel = Hotel::createHotel($hotelData);

                return redirect()
                    ->route('adminHotelCreatePage')
                    ->with('success', 'ホテル「' . $hotel->hotel_name . '」を正常に作成しました。');
            } catch (\Exception $e) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['error' => 'ホテルの作成中にエラーが発生しました。もう一度お試しください。']);
            }
        });
    }

    public function delete(Request $request): RedirectResponse
    {
        return DB::transaction(function () use ($request) {

            $hotelId = $request->input('hotel_id');
            if (!$hotelId) {
                return redirect()->back()->withErrors(['error' => 'ホテルIDが指定されていません。']);
            }
            $hotel = Hotel::find($hotelId);
            if (!$hotel) {
                return redirect()->back()->withErrors(['error' => 'ホテルが見つかりません。']);
            }
            // Xóa file ảnh nếu có
            if ($hotel->file_path && Storage::disk('public')->exists($hotel->file_path)) {
                Storage::disk('public')->delete($hotel->file_path);
            }
            Hotel::deleteHotel($hotelId);

            // fresh data
            $hotelList = Hotel::getHotelListByName('');

            return redirect()
                ->route('adminHotelSearchResult')
                ->with('hotelList', $hotelList)
                ->with('success', 'ホテル「' . $hotel->hotel_name . '」を削除しました。');
        });
    }
}

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
use Illuminate\Support\Str;

class HotelController extends Controller
{
    /** get methods */

    public function showSearch(): View
    {
        $prefectures = Prefecture::getAllPrefectures();
        return view('admin.hotel.search', compact('prefectures'));
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

    public function searchResult(Request $request): View|RedirectResponse
    {
        $var = [];
        $hotelName = $request->input('hotel_name');
        $prefectureId = $request->input('prefecture_id');

        // Check if search name empty
        if (empty(trim($hotelName))) {
            return redirect()->route('adminHotelSearchPage')
                ->withErrors(['searchError' => '何も入力されていません。']);
        }

        // Search hotels by name and/or prefecture
        $hotelList = Hotel::getHotelListByConditions([
            'hotel_name' => $hotelName,
            'prefecture_id' => $prefectureId,
        ]);
        $prefectures = Prefecture::getAllPrefectures();
        return view('admin.hotel.result', compact('hotelList', 'prefectures'));
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
                $newFilePath = $currentFilePath; // Keep current path if no new file uploaded

                // Handle new file upload
                if ($request->hasFile('hotel_image')) {
                    $file = $request->file('hotel_image');
                    $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

                    // Store file in public/assets/img/hotel
                    $destinationPath = public_path('assets/img/hotel');
                    $file->move($destinationPath, $fileName);
                    $newFilePath = 'hotel/' . $fileName;

                    // Delete old file if exists
                    if ($currentFilePath && $currentFilePath !== $newFilePath) {
                        $oldFilePath = public_path('assets/img/' . $currentFilePath);
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
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

                    // Store file in public/assets/img/hotel
                    $destinationPath = public_path('assets/img/hotel');
                    $file->move($destinationPath, $fileName);
                    $filePath = 'hotel/' . $fileName;
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

    public function delete(Request $request): View|RedirectResponse
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
            // remove image file
            if ($hotel->file_path) {
                $filePath = public_path($hotel->file_path);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            Hotel::deleteHotel($hotelId);

            // fresh data
            $hotelList = Hotel::getHotelListByConditions();
            $prefectures = Prefecture::getAllPrefectures();

            return view('admin.hotel.result', compact('hotelList', 'prefectures'))
                ->with('success', 'ホテル「' . $hotel->hotel_name . '」を削除しました。');
        });
    }
}

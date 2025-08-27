<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hotel extends Model
{
    /**
     * @var string
     */
    protected $primaryKey = 'hotel_id';

    /**
     * @var array
     */
    protected $guarded = ['hotel_id'];

    /**
     * @var array
     */
    protected $fillable = [
        'hotel_name',
        'prefecture_id',
        'file_path',
    ];

    /**
     * @return BelongsTo
     */
    public function prefecture(): BelongsTo
    {
        return $this->belongsTo(Prefecture::class, 'prefecture_id', 'prefecture_id');
    }

    /**
     * Search hotel by hotel name
     *
     * @param array $params
     * @return array
     */
    static public function getHotelListByConditions(array $params = []): array
    {
        $result = Hotel::with('prefecture')
            ->when(!empty($params['hotel_name']), function ($query) use ($params) {
                $query->where('hotel_name', 'like', '%' . $params['hotel_name'] . '%');
            })
            ->when(!empty($params['prefecture_id']), function ($query) use ($params) {
                $query->whereHas('prefecture', function ($q) use ($params) {
                    $q->where('prefecture_id', $params['prefecture_id']);
                });
            })
            ->get()
            ->toArray();

        return $result;
    }

    /**
     * Override serializeDate method to customize date format
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Create a new hotel
     *
     * @param array $data
     * @return Hotel
     */
    public static function createHotel(array $data): Hotel
    {
        return self::create([
            'hotel_name' => $data['hotel_name'],
            'prefecture_id' => $data['prefecture_id'],
            'file_path' => $data['file_path'] ?? null,
        ]);
    }

    /**
     * Update an existing hotel
     *
     * @param int $hotelId
     * @param array $data
     * @return Hotel
     */
    public static function updateHotel(int $hotelId, array $data): Hotel
    {
        $hotel = self::findOrFail($hotelId);

        $updateData = [
            'hotel_name' => $data['hotel_name'],
            'prefecture_id' => $data['prefecture_id'],
        ];

        // Only update file_path if provided
        if (isset($data['file_path'])) {
            $updateData['file_path'] = $data['file_path'];
        }

        $hotel->update($updateData);

        return $hotel->fresh();
    }

    /**
     * Delete a hotel by ID
     *
     * @param int $hotelId
     * @return bool
     */
    public static function deleteHotel(int $hotelId): bool
    {
        $hotel = Hotel::findOrFail($hotelId);
        return $hotel->delete();
    }
}

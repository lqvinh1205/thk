<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
	/**
	 * @var string
	 */
	protected $primaryKey = 'booking_id';

	/**
	 * @var array
	 */
	protected $guarded = ['booking_id'];

	/**
	 * @var array
	 */
	protected $fillable = [
		'hotel_id',
		'customer_name',
		'customer_contact',
		'checkin_time',
		'checkout_time',
	];

	/**
	 * @var array
	 */
	protected $casts = [
		'checkin_time' => 'datetime',
		'checkout_time' => 'datetime',
	];

	/**
	 * @return BelongsTo
	 */
	public function hotel(): BelongsTo
	{
		return $this->belongsTo(Hotel::class, 'hotel_id', 'hotel_id');
	}

	/**
	 * Search bookings by conditions
	 *
	 * @param array $params
	 * @return array
	 */
	static public function getBookingListByConditions(array $params = []): array
	{
		$result = Booking::with('hotel')
			->when(!empty($params['customer_name']), function ($query) use ($params) {
				$query->where('customer_name', 'like', '%' . $params['customer_name'] . '%');
			})
			->when(!empty($params['customer_contact']), function ($query) use ($params) {
				$query->where('customer_contact', 'like', '%' . $params['customer_contact'] . '%');
			})
			->when(!empty($params['checkin_date']), function ($query) use ($params) {
				$query->whereDate('checkin_time', $params['checkin_date']);
			})
			->when(!empty($params['checkout_date']), function ($query) use ($params) {
				$query->whereDate('checkout_time', $params['checkout_date']);
			})
			->orderBy('created_at', 'desc')
			->get()
			->toArray();

		return $result;
	}
}

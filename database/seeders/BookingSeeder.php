<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$bookings = [
			[
				'hotel_id' => 1,
				'customer_name' => 'Customer A',
				'customer_contact' => '03-1234-5678',
				'checkin_time' => Carbon::parse('2025-09-01 15:00:00'),
				'checkout_time' => Carbon::parse('2025-09-03 11:00:00'),
				'created_at' => Carbon::parse('2025-08-20 10:30:00'),
				'updated_at' => Carbon::parse('2025-08-20 10:30:00'),
			],
			[
				'hotel_id' => 2,
				'customer_name' => 'Customer B',
				'customer_contact' => 'customerb@email.com',
				'checkin_time' => Carbon::parse('2025-09-05 14:00:00'),
				'checkout_time' => Carbon::parse('2025-09-07 10:00:00'),
				'created_at' => Carbon::parse('2025-08-22 14:15:00'),
				'updated_at' => Carbon::parse('2025-08-23 09:20:00'),
			],
			[
				'hotel_id' => 1,
				'customer_name' => 'Customer C',
				'customer_contact' => '090-9876-5432',
				'checkin_time' => Carbon::parse('2025-09-10 16:00:00'),
				'checkout_time' => Carbon::parse('2025-09-12 12:00:00'),
				'created_at' => Carbon::parse('2025-08-25 16:45:00'),
				'updated_at' => Carbon::parse('2025-08-25 16:45:00'),
			],
			[
				'hotel_id' => 3,
				'customer_name' => 'Customer D',
				'customer_contact' => 'customerb@gmail.com',
				'checkin_time' => Carbon::parse('2025-09-15 15:30:00'),
				'checkout_time' => Carbon::parse('2025-09-17 11:30:00'),
				'created_at' => Carbon::parse('2025-08-26 11:00:00'),
				'updated_at' => Carbon::parse('2025-08-27 08:15:00'),
			],
			[
				'hotel_id' => 2,
				'customer_name' => 'Customer E',
				'customer_contact' => '06-5555-9999',
				'checkin_time' => Carbon::parse('2025-09-20 13:00:00'),
				'checkout_time' => Carbon::parse('2025-09-22 10:00:00'),
				'created_at' => Carbon::parse('2025-08-27 13:30:00'),
				'updated_at' => Carbon::parse('2025-08-27 13:30:00'),
			],
		];

		foreach ($bookings as $booking) {
			DB::table('bookings')->insert($booking);
		}
	}
}

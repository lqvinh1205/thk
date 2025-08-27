<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Booking;

class BookingController extends Controller
{
	/**
	 * Show booking search page
	 *
	 * @return View
	 */
	public function showSearch(): View
	{
		return view('admin.booking.search');
	}

	/**
	 * Search bookings by conditions
	 *
	 * @param Request $request
	 * @return View|RedirectResponse
	 */
	public function searchResult(Request $request): View|RedirectResponse
	{
		$customerName = $request->input('customer_name');
		$customerContact = $request->input('customer_contact');
		$checkinDate = $request->input('checkin_date');
		$checkoutDate = $request->input('checkout_date');

		// Search bookings by conditions
		$bookingList = Booking::getBookingListByConditions([
			'customer_name' => $customerName,
			'customer_contact' => $customerContact,
			'checkin_date' => $checkinDate,
			'checkout_date' => $checkoutDate,
		]);

		return view('admin.booking.result', compact('bookingList'));
	}
}

@extends('admin.booking.search')
@section('search_results')
<div class="page-wrapper search-page-wrapper">
	<div class="search-result">
		<h3 class="search-result-title">検索結果</h3>
		<!-- Success Message -->
		@if(session('success'))
		<div class="alert alert-success">
			{{ session('success') }}
		</div>
		@endif

		<!-- Error Messages -->
		@if($errors->any())
		<div class="alert alert-error">
			<ul>
				@foreach($errors->all() as $error)
				<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
		@endif

		@if (!empty($bookingList))
		<table class="shopsearchlist_table">
			<tbody>
				<tr>
					<td nowrap="" id="customer_name">
						顧客名
					</td>
					<td nowrap="" id="customer_contact">
						顧客連絡先
					</td>
					<td nowrap="" id="checkin_time">
						チェックイン日時
					</td>
					<td nowrap="" id="checkout_time">
						チェックアウト日時
					</td>
					<td nowrap="" id="hotel_name">
						ホテル名
					</td>
					<td nowrap="" id="created_at">
						予約日時
					</td>
					<td nowrap="" id="updated_at">
						情報更新日時
					</td>
				</tr>
				@foreach($bookingList as $booking)
				<tr style="background-color:#BDF1FF">
					<td>
						{{ $booking['customer_name'] }}
					</td>
					<td>
						{{ $booking['customer_contact'] }}
					</td>
					<td>
						{{ \Carbon\Carbon::parse($booking['checkin_time'])->format('Y-m-d H:i') }}
					</td>
					<td>
						{{ \Carbon\Carbon::parse($booking['checkout_time'])->format('Y-m-d H:i') }}
					</td>
					<td>
						{{ $booking['hotel']['hotel_name'] ?? 'N/A' }}
					</td>
					<td>
						{{ \Carbon\Carbon::parse($booking['created_at'])->format('Y-m-d H:i:s') }}
					</td>
					<td>
						{{ \Carbon\Carbon::parse($booking['updated_at'])->format('Y-m-d H:i:s') }}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
		@else
		<p>検索結果がありません</p>
		@endif
	</div>
</div>
@endsection
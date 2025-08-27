<!-- base view -->
@extends('common.admin.base')

<!-- CSS per page -->
@section('custom_css')
@vite('resources/scss/admin/search.scss')
@vite('resources/scss/admin/result.scss')
@endsection

<!-- main contents -->
@section('main_contents')
<div class="page-wrapper search-page-wrapper">
	<h2 class="title">予約情報検索画面</h2>
	<hr>
	<div class="search-booking">
		<form action="{{ route('adminBookingSearchResult') }}" method="post">
			@csrf
			<div class="search-form-group">
				<div class="form-row">
					<div class="form-group">
						<label for="customer_name">顧客名</label>
						<div>
							<input type="text" id="customer_name" name="customer_name"
								value="{{ old('customer_name', request('customer_name', '')) }}"
								placeholder="顧客名を入力してください" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="checkin_date">チェックイン日時</label>
						<div>
							<input type="date" id="checkin_date" name="checkin_date"
								value="{{ old('checkin_date', request('checkin_date', '')) }}"
								class="form-control">
						</div>

					</div>
				</div>
				<div class="form-row">
					<div class="form-group">
						<label for="customer_contact">顧客連絡先</label>
						<div>
							<input type="text" id="customer_contact" name="customer_contact"
								value="{{ old('customer_contact', request('customer_contact', '')) }}"
								placeholder="顧客連絡先を入力してください" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="checkout_date">チェックアウト日時</label>
						<div>
							<input type="date" id="checkout_date" name="checkout_date"
								value="{{ old('checkout_date', request('checkout_date', '')) }}"
								class="form-control">
						</div>
					</div>
				</div>
				<div class="search-button-group">
					<button type="submit" class="btn btn-primary">検索</button>
				</div>
				@error('searchError')
				<div class="form-row">
					<span class="error-message">{{ $message }}</span>
				</div>
				@enderror
			</div>
		</form>
	</div>
	<hr>
</div>
@yield('search_results')
@endsection
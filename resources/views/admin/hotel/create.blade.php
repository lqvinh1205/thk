<!-- base view -->
@extends('common.admin.base')

<!-- CSS per page -->
@section('custom_css')
@vite('resources/scss/admin/create.scss')
@endsection

<!-- main contents -->
@section('main_contents')
<div class="page-wrapper search-page-wrapper">
	<h2 class="title">ホテル新規作成</h2>
	<hr>

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

	<div class="create-hotel-form">
		<form action="{{ route('adminHotelCreateProcess') }}" method="post" enctype="multipart/form-data">
			@csrf

			<!-- Hotel Name -->
			<div class="form-group">
				<label for="hotel_name">ホテル名 <span class="required">*</span></label>
				<input
					type="text"
					id="hotel_name"
					name="hotel_name"
					value="{{ old('hotel_name') }}"
					placeholder="ホテル名を入力してください"
					class="form-control @error('hotel_name') error @enderror"
					required>
				@error('hotel_name')
				<span class="error-message">{{ $message }}</span>
				@enderror
			</div>

			<!-- Prefecture -->
			<div class="form-group">
				<label for="prefecture_id">都道府県 <span class="required">*</span></label>
				<select
					id="prefecture_id"
					name="prefecture_id"
					class="form-control @error('prefecture_id') error @enderror"
					required>
					<option value="">都道府県を選択してください</option>
					@foreach($prefectures as $prefecture)
					<option
						value="{{ $prefecture->prefecture_id }}"
						{{ old('prefecture_id') == $prefecture->prefecture_id ? 'selected' : '' }}>
						{{ $prefecture->prefecture_name }}
					</option>
					@endforeach
				</select>
				@error('prefecture_id')
				<span class="error-message">{{ $message }}</span>
				@enderror
			</div>

			<!-- Hotel Image -->
			<div class="form-group">
				<label for="hotel_image">ホテル画像</label>
				<input
					type="file"
					id="hotel_image"
					name="hotel_image"
					accept="image/*"
					class="form-control @error('hotel_image') error @enderror">
				<small class="form-text">JPEGまたはPNG形式、最大5MBまで</small>
				@error('hotel_image')
				<span class="error-message">{{ $message }}</span>
				@enderror
			</div>

			<!-- Submit Buttons -->
			<div class="form-actions">
				<button type="submit" class="btn btn-primary">作成</button>
				<a href="{{ route('adminHotelSearchPage') }}" class="btn btn-secondary">キャンセル</a>
			</div>
		</form>
	</div>
	<hr>
</div>
@endsection

<!-- Page specific JavaScript -->
@section('page_js')
<script>
	document.addEventListener('DOMContentLoaded', function() {
		// File input preview functionality
		const fileInput = document.getElementById('hotel_image');
		if (fileInput) {
			fileInput.addEventListener('change', function(e) {
				const file = e.target.files[0];
				if (file) {
					// Validate file size (5MB)
					if (file.size > 5 * 1024 * 1024) {
						alert('ファイルサイズが大きすぎます。5MB以下のファイルを選択してください。');
						this.value = '';
						return;
					}

					// Validate file type
					if (!file.type.match('image.*')) {
						alert('画像ファイルを選択してください。');
						this.value = '';
						return;
					}
				}
			});
		}

		// Form validation
		const form = document.querySelector('form');
		if (form) {
			form.addEventListener('submit', function(e) {
				const hotelName = document.getElementById('hotel_name').value.trim();
				const prefectureId = document.getElementById('prefecture_id').value;

				if (!hotelName) {
					alert('ホテル名を入力してください。');
					e.preventDefault();
					return;
				}

				if (!prefectureId) {
					alert('都道府県を選択してください。');
					e.preventDefault();
					return;
				}
			});
		}
	});
</script>
@endsection
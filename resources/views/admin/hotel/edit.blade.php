<!-- base view -->
@extends('common.admin.base')

<!-- CSS per page -->
@section('custom_css')
@vite('resources/scss/admin/edit.scss')
@endsection

<!-- main contents -->
@section('main_contents')
<div class="page-wrapper search-page-wrapper">
	<h2 class="title">ホテル編集</h2>
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
		<form action="{{ route('adminHotelEditProcess') }}" method="post" enctype="multipart/form-data">
			@csrf
			<input type="hidden" name="hotel_id" value="{{ $hotel->hotel_id }}">

			<!-- Hotel Name -->
			<div class="form-group">
				<label for="hotel_name">ホテル名 <span class="required">*</span></label>
				<input
					type="text"
					id="hotel_name"
					name="hotel_name"
					value="{{ old('hotel_name', $hotel->hotel_name) }}"
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
						{{ (old('prefecture_id', $hotel->prefecture_id) == $prefecture->prefecture_id) ? 'selected' : '' }}>
						{{ $prefecture->prefecture_name }}
					</option>
					@endforeach
				</select>
				@error('prefecture_id')
				<span class="error-message">{{ $message }}</span>
				@enderror
			</div>

			<!-- Current Hotel Image -->
			@if($hotel->file_path)
			<div class="form-group">
				<label>現在の画像</label>
				<div class="current-image">
					<img src="{{ asset('storage/' . $hotel->file_path) }}" alt="{{ $hotel->hotel_name }}" style="width: 150px; height: 150px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
				</div>
			</div>
			@endif

			<!-- Hotel Image -->
			<div class="form-group">
				<label for="hotel_image">
					@if($hotel->file_path)
					新しいホテル画像 (選択すると現在の画像を置き換えます)
					@else
					ホテル画像
					@endif
				</label>
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
				<button type="submit" class="btn btn-primary">更新</button>
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
		const removeImageCheckbox = document.querySelector('input[name="remove_image"]');
		const currentImageDiv = document.querySelector('.current-image');

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

					// If new file is selected, uncheck remove image
					if (removeImageCheckbox) {
						removeImageCheckbox.checked = false;
					}
				}
			});
		}

		// Remove image checkbox functionality
		if (removeImageCheckbox) {
			removeImageCheckbox.addEventListener('change', function() {
				if (this.checked && fileInput) {
					// If remove is checked, clear file input
					fileInput.value = '';
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
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
					<img src="{{ '/assets/img/' . $hotel->file_path }}" alt="{{ $hotel->hotel_name }}" style="width: 150px; height: 150px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
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
				<button type="button" id="confirm-update-btn" class="btn btn-primary">更新</button>
				<a href="{{ route('adminHotelSearchPage') }}" class="btn btn-secondary">キャンセル</a>
			</div>
		</form>
	</div>

	<!-- Confirmation Modal -->
	<div id="confirmation-modal" class="modal-overlay" style="display: none;">
		<div class="modal-content">
			<h3>更新内容確認</h3>
			<div class="confirmation-details">
				<div class="detail-row">
					<label>ホテル名:</label>
					<span id="confirm-hotel-name"></span>
				</div>
				<div class="detail-row">
					<label>都道府県:</label>
					<span id="confirm-prefecture"></span>
				</div>
				<div class="detail-row">
					<label>画像:</label>
					<span id="confirm-image"></span>
				</div>
			</div>
			<div class="modal-actions">
				<button type="button" id="confirm-submit-btn" class="btn btn-primary">確定</button>
				<button type="button" id="cancel-confirm-btn" class="btn btn-secondary">キャンセル</button>
			</div>
		</div>
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
		const form = document.querySelector('form');
		const confirmUpdateBtn = document.getElementById('confirm-update-btn');
		const modal = document.getElementById('confirmation-modal');
		const confirmSubmitBtn = document.getElementById('confirm-submit-btn');
		const cancelConfirmBtn = document.getElementById('cancel-confirm-btn');

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

		// Confirmation modal functionality
		if (confirmUpdateBtn) {
			confirmUpdateBtn.addEventListener('click', function(e) {
				e.preventDefault();

				// Validate form first
				const hotelName = document.getElementById('hotel_name').value.trim();
				const prefectureId = document.getElementById('prefecture_id').value;

				if (!hotelName) {
					alert('ホテル名を入力してください。');
					return;
				}

				if (!prefectureId) {
					alert('都道府県を選択してください。');
					return;
				}

				// Get form data for confirmation
				const prefectureSelect = document.getElementById('prefecture_id');
				const prefectureName = prefectureSelect.options[prefectureSelect.selectedIndex].text;
				const imageFile = fileInput.files[0];

				// Fill confirmation modal
				document.getElementById('confirm-hotel-name').textContent = hotelName;
				document.getElementById('confirm-prefecture').textContent = prefectureName;

				// Handle image confirmation text
				let imageText = '変更なし';
				if (imageFile) {
					imageText = '新しい画像: ' + imageFile.name;
				} else if (removeImageCheckbox && removeImageCheckbox.checked) {
					imageText = '画像を削除';
				}
				document.getElementById('confirm-image').textContent = imageText;

				// Show modal
				modal.style.display = 'flex';
			});
		}

		// Confirm submit button
		if (confirmSubmitBtn) {
			confirmSubmitBtn.addEventListener('click', function() {
				modal.style.display = 'none';
				form.submit();
			});
		}

		// Cancel confirmation button
		if (cancelConfirmBtn) {
			cancelConfirmBtn.addEventListener('click', function() {
				modal.style.display = 'none';
			});
		}

		// Close modal when clicking outside
		modal.addEventListener('click', function(e) {
			if (e.target === modal) {
				modal.style.display = 'none';
			}
		});
	});
</script>
@endsection
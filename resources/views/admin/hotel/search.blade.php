<!-- base view -->
@extends('common.admin.base')

<!-- CSS per page -->
@section('custom_css')
@vite('resources/scss/admin/search.scss')
@vite('resources/scss/admin/result.scss')
@endsection

<!-- main containts -->
@section('main_contents')
<div class="page-wrapper search-page-wrapper">
    <h2 class="title">検索画面</h2>
    <hr>
    <div class="search-hotel-name">
        <form action="{{ route('adminHotelSearchResult') }}" method="post">
            @csrf
            <div class="search-form-group">
                <div class="form-row">
                    <div class="form-group">
                        <label for="hotel_name">ホテル名</label>
                        <div class="input-wrapper">
                            <input type="text" id="hotel_name" name="hotel_name" value="{{ old('hotel_name', request('hotel_name', '')) }}" placeholder="ホテル名を入力してください" class="form-control">
                            @error('searchError')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="prefecture_id">都道府県</label>
                        <div class="input-wrapper">
                            <select id="prefecture_id" name="prefecture_id" class="form-control">
                                <option value="">都道府県を選択してください</option>
                                @if(isset($prefectures))
                                @foreach($prefectures as $prefecture)
                                <option value="{{ $prefecture['prefecture_id'] }}"
                                    {{ old('prefecture_id', request('prefecture_id')) == $prefecture['prefecture_id'] ? 'selected' : '' }}>
                                    {{ $prefecture['prefecture_name'] }}
                                </option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="search-button-group">
                    <button type="submit" class="btn btn-primary">検索</button>
                </div>
            </div>
        </form>
    </div>
    <hr>
</div>
@yield('search_results')
@endsection
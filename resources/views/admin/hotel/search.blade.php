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
        <form action="{{ route('adminHotelSearchResult') }}" method="get">
            @csrf
            <div class="search-input-group">
                <input type="text" name="hotel_name" value="{{ old('hotel_name', '') }}" placeholder="ホテル名">
                <button type="submit">検索</button>
            </div>
            @if(!empty($searchError))
            <span class="error-message">{{ $searchError }}</span>
            @endif
        </form>
    </div>
    <hr>
</div>
@yield('search_results')
@endsection
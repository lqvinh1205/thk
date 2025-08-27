@extends('admin.hotel.search')
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

        @if (!empty($hotelList))
        <table class="shopsearchlist_table">
            <tbody>
                <tr>
                    <td nowrap="" id="hotel_name">
                        ホテル名
                    </td>
                    <td nowrap="" id="pref">
                        都道府県
                    </td>
                    <td nowrap="" id="created_at">
                        登録日
                    </td>
                    <td nowrap="" id="updated_at">
                        更新日
                    </td>
                    <td class="btn_center" id="edit"></td>
                    <td class="btn_center" id="delete"></td>
                </tr>
                @foreach($hotelList as $hotel)
                <tr style="background-color:#BDF1FF">
                    <td>
                        <a href="" target="_blank">{{ $hotel['hotel_name'] }}</a>
                    </td>
                    <td>
                        {{ $hotel['prefecture']['prefecture_name'] }}
                    </td>
                    <td>
                        {{ (string) $hotel['created_at'] }}
                    </td>
                    <td>
                        {{ (string) $hotel['updated_at'] }}
                    </td>
                    <td>
                        <form action="{{ route('adminHotelEditPage') }}" method="get">
                            @csrf
                            <input type="hidden" name="hotel_id" value="{{ $hotel['hotel_id'] }}">
                            <button type="submit">編集</button>
                        </form>
                    </td>
                    <td>
                        <form action="{{ route('adminHotelDeleteProcess') }}" method="post" class="delete-hotel-form">
                            @csrf
                            <input type="hidden" name="hotel_id" value="{{ $hotel['hotel_id'] }}">
                            <input type="hidden" name="search_term" value="{{ $current_search ?? '' }}">
                            <button type="submit" class="btn-delete">削除</button>
                        </form>
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

<!-- Page specific JavaScript -->
@section('page_js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-hotel-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                if (!confirm('本当にこのホテルを削除しますか？\n\nこの操作は元に戻せません。')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endsection
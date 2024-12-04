@extends('layouts.app')

@section('content')
    @if(session('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
    @endif

<div class="container">
    <h1 class="mb-4">商品情報一覧</h1>

    <div class="search mt-5">
        <h2>検索条件で絞り込み</h2>
        <form id="search-form" action="{{ route('products.index') }}" method="GET" class="row g-3">
            <div class="col-sm-12 col-md-3">
                <input type="text" name="search" class="form-control" placeholder="商品名" value="{{ request('search') }}">
            </div>
            <div class="col-sm-12 col-md-2">
                <input type="number" name="min_price" class="form-control" placeholder="最小価格" value="{{ request('min_price') }}">
            </div>
            <div class="col-sm-12 col-md-2">
                <input type="number" name="max_price" class="form-control" placeholder="最大価格" value="{{ request('max_price') }}">
            </div>
            <div class="col-sm-12 col-md-2">
                <input type="number" name="min_stock" class="form-control" placeholder="最小在庫" value="{{ request('min_stock') }}">
            </div>
            <div class="col-sm-12 col-md-2">
                <input type="number" name="max_stock" class="form-control" placeholder="最大在庫" value="{{ request('max_stock') }}">
            </div>
            <div class="col-sm-12 col-md-3">
                <select class="form-select" id="company_id" name="company_id">
                    <option value="">すべてのメーカー</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->company_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-12 col-md-1">
                <button class="btn btn-outline-secondary" type="submit" id="search-button">検索</button>
            </div>
        </form>
        <a href="{{ route('products.index') }}" class="btn btn-success mt-3">検索条件を元に戻す</a>
    </div>

    <!-- 検索結果表示 -->
    <div id="search-results" class="products mt-5">
        @include('products.index-content', ['products' => $products])
    </div>
</div>
@endsection

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function () {
        console.log('JavaScript is working'); //デバッグ用

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#search-button').on('click', function (event) {
            event.preventDefault(); // デフォルトのフォーム送信をキャンセル
            console.log('Search button clicked'); //デバッグ用
            let formData = $('#search-form').serialize();
            console.log('Sending AJAX request with data:', formData);

            $.ajax({
                url: '{{ route("products.index") }}',
                type: 'GET',
                data: formData,
                success: function(data) {
                    $('#search-results').html(data.html);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX request failed:', error);
                    alert('検索中にエラーが発生しました: ' + error);
                }
            });
        });

        $(document).on('click', '.delete-button', function () {
            let deleteConfirm = confirm('削除してよろしいでしょうか？');
            if (deleteConfirm) {
                let form = $(this).closest('form');
                let productID = form.data('product_id');
                console.log('Sending DELETE request for product ID:', productID);

                // ベースURLを設定
                let baseURL = '/step7/public';

                $.ajax({
                    url: `${baseURL}/products/${productID}`,
                    type: 'POST',
                    data: {
                        _method: 'DELETE', // DELETEメソッドにオーバーライド
                        _token: $('meta[name="csrf-token"]').attr('content') // CSRFトークンを追加
                    },
                    success: function(response) {
                        console.log('DELETE request successful:', response);
                        form.closest('tr').remove();

                        // 成功メッセージを表示
                        $('.mb-4').prepend(`
                            <div class="alert alert-success">${response.message}</div>
                        `);
                    },
                    error: function(xhr, status, error) {
                        console.error('DELETE request failed:', error);
                        alert('削除中にエラーが発生しました: ' + error);
                    }
                });
            }
        });
    });
</script>

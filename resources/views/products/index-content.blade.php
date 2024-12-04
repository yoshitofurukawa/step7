<table class="table table-striped">
    <thead>
        <tr>
            <th>ID
                @php
                    $currentDirection = request()->get('direction', 'asc');
                    $newDirection = $currentDirection === 'asc' ? 'desc' : 'asc';
                @endphp
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'direction' => $newDirection]) }}">
                    {{ $currentDirection === 'asc' ? '昇順' : '降順' }}
                </a>
            </th>
            <th>商品画像</th>
            <th>商品名</th>
            <th>価格
                @php
                    $currentDirection = request()->get('direction', 'asc');
                    $newDirection = $currentDirection === 'asc' ? 'desc' : 'asc';
                @endphp
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'price', 'direction' => $newDirection]) }}">
                    {{ $currentDirection === 'asc' ? '昇順' : '降順' }}
                </a>
            </th>
            <th>在庫数
                @php
                    $currentDirection = request()->get('direction', 'asc');
                    $newDirection = $currentDirection === 'asc' ? 'desc' : 'asc';
                @endphp
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'stock', 'direction' => $newDirection]) }}">
                    {{ $currentDirection === 'asc' ? '昇順' : '降順' }}
                </a>
            </th>
            <th>メーカー名</th>
            <th><a href="{{ route('products.create') }}" class="btn btn-primary mb-3">新規登録</a></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $product)
        <tr>
            <td>{{ $product->id }}.</td>
            <td><img src="{{ asset($product->img_path) }}" alt="商品画像" width="100"></td>
            <td>{{ $product->product_name }}</td>
            <td>￥{{ $product->price }}</td>
            <td>{{ $product->stock }}</td>
            <td>{{ $product->company->company_name }}</td>
            <td>
                <a href="{{ route('products.show', $product) }}" class="btn btn-info btn-sm mx-1">詳細表示</a>
                <form method="POST" class="d-inline delete-form" data-product_id="{{ $product->id }}">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-danger btn-sm mx-1 delete-button">削除</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $products->appends(request()->query())->links() }}

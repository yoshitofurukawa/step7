<?php

namespace App\Http\Controllers;

use App\Models\Product; 
use App\Models\Company; 
use Illuminate\Http\Request; 
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller 
{
    
    public function index(Request $request)
    {
    // クエリビルダを初期化
    $query = Product::query();
    // 商品名検索クエリに追加
    if($search = $request->search){
        $query->where('product_name', 'LIKE', "%{$search}%");
    }
    // 最小価格指定クエリに追加
    if($min_price = $request->min_price){
        $query->where('price', '>=', $min_price);
    }

    // 最大価格指定クエリに追加
    if($max_price = $request->max_price){
        $query->where('price', '<=', $max_price);
    }

    // 最小在庫数指定クエリに追加
    if($min_stock = $request->min_stock){
        $query->where('stock', '>=', $min_stock);
    }

    // 最大在庫数指定クエリに追加
    if($max_stock = $request->max_stock){
        $query->where('stock', '<=', $max_stock);
    }

    // メーカー名検索用
    if($company_id = $request->company_id){
        $query->where('company_id', $company_id);
    }
    // ソート機能
    if($sort = $request->sort){
        $direction = $request->direction == 'desc' ? 'desc' : 'asc'; // directionがdescでない場合は、デフォルトでascとする
        $query->orderBy($sort, $direction);
    }
    
    // ページネーション
    $products = $query->paginate(10);
    
    if ($request->ajax()) {
        return view('products.index', compact('products'))->renderSections()['content'];
    }

    $companies = Company::all();
    // ビューに渡す
    return view('products.index', compact('products','companies'));

    }



    public function create()
    {
        $companies = Company::all();

        return view('products.create', compact('companies'));
    }

    public function store(ProductRequest $request) 
    {
        DB::beginTransaction();

    try {
        $product = new Product([
            'product_name' => $request->get('product_name'),
            'company_id' => $request->get('company_id'),
            'price' => $request->get('price'),
            'stock' => $request->get('stock'),
            'comment' => $request->get('comment'),
        ]);


        if($request->hasFile('img_path')){ 
            $filename = $request->img_path->getClientOriginalName();
            $filePath = $request->img_path->storeAs('products', $filename, 'public');
            $product->img_path = '/storage/' . $filePath;
        }

        $product->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            info($e->getMessage()); 
        }

        // 全ての処理が終わったら、商品一覧画面に戻ります。
        return redirect('products');
    }

    public function show(Product $product)
    {
        return view('products.show', ['product' => $product]);
    }

    public function edit(Product $product)
    {
        // 全ての会社の情報を取得
        $companies = Company::all();
        return view('products.edit', compact('product', 'companies'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        DB::beginTransaction();

    try {
        // 商品の情報を更新
        $product->product_name = $request->product_name;
        $product->company_id = $request->company_id;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->comment = $request->comment;
        if($request->hasFile('img_path')){ 
            $filename = $request->img_path->getClientOriginalName();
            $filePath = $request->img_path->storeAs('products', $filename, 'public');
            $product->img_path = '/storage/' . $filePath;
        }

        // 更新した商品を保存
        $product->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            info($e->getMessage()); 
        }
        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully');
        // ビュー画面にメッセージを代入した変数(success)を送ります
    }

    public function destroy(Product $product)
    {
        try {
            $product->delete();
            return response()->json(['message' => '削除されました'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => '削除中にエラーが発生しました: ' . $e->getMessage()], 500);
        }
    }
    

}
<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\CatalogProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    private function resolveProductId(int $id): ?int
    {
        // If already a Product id, return as is
        $product = Product::find($id);
        if ($product) {
            return $product->id;
        }
        // Try to resolve from CatalogProduct and ensure a Product exists
        $catalog = CatalogProduct::find($id);
        if (!$catalog) {
            return null;
        }
        $product = Product::where('name', $catalog->name)
            ->where('price', $catalog->price)
            ->where('category', $catalog->category)
            ->first();
        if (!$product) {
            $product = new Product([
                'code' => null,
                'name' => $catalog->name,
                'description' => $catalog->description,
                'price' => $catalog->price,
                'stock' => 0,
                'category' => $catalog->category,
                'image' => $catalog->image,
                'image2' => $catalog->image2,
                'image3' => $catalog->image3,
                'status' => true,
                'is_approved' => true,
            ]);
            $product->save();
        }
        return $product->id;
    }
    public function index()
    {
        $favorites = Favorite::with(['product','catalogProduct'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        // Self-heal: if a favorite points to a catalog id, resolve and update
        foreach ($favorites as $fav) {
            if (!$fav->product) {
                $resolvedId = $this->resolveProductId($fav->product_id);
                if ($resolvedId && $resolvedId !== (int)$fav->product_id) {
                    $fav->product_id = $resolvedId;
                    $fav->save();
                    $fav->load('product');
                }
            }
        }

        $data = $favorites->map(function ($f) {
                // Prefer catalog info if present (user added from catalog)
                if ($f->catalogProduct) {
                    $c = $f->catalogProduct;
                    return [
                        'id' => $c->id,
                        'name' => $c->name,
                        'price' => $c->price,
                        'image' => $c->image ? asset('storage/' . $c->image) : null,
                    ];
                }
                if ($f->product) {
                    $p = $f->product;
                    return [
                        'id' => $p->id,
                        'name' => $p->name,
                        'price' => $p->price,
                        'image' => $p->image ? asset('storage/' . $p->image) : null,
                    ];
                }
                return null;
            })->filter()->values();

        return response()->json(['favorites' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate(['product_id' => 'required|integer']);
        $incomingId = (int)$request->input('product_id');
        $productId = $this->resolveProductId($incomingId);
        if (!$productId) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $fav = Favorite::firstOrCreate([
            'user_id' => Auth::id(),
            'product_id' => $productId,
            'catalog_product_id' => CatalogProduct::find($incomingId) ? $incomingId : null,
        ]);
        return response()->json(['status' => 'added']);
    }

    public function destroy($productId)
    {
        $resolved = $this->resolveProductId((int)$productId);
        Favorite::where('user_id', Auth::id())
            ->where(function($q) use ($resolved, $productId){
                $q->where('product_id', $resolved)
                  ->orWhere('catalog_product_id', (int)$productId);
            })
            ->delete();
        return response()->json(['status' => 'removed']);
    }

    public function check($productId)
    {
        $resolved = $this->resolveProductId((int)$productId);
        $exists = Favorite::where('user_id', Auth::id())
            ->where(function($q) use ($resolved, $productId){
                $q->where('product_id', $resolved)
                  ->orWhere('catalog_product_id', (int)$productId);
            })
            ->exists();
        return response()->json(['favored' => $exists]);
    }
}

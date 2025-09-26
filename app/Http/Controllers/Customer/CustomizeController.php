<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;

class CustomizeController extends Controller
{
    public function index()
    {
        $categories = ['Wrapper','Focal','Greeneries','Ribbons','Fillers'];
        $items = Product::whereIn('category',$categories)->orderBy('category')->orderBy('name')->get()->groupBy('category');
        return view('products.bouquet-customize', compact('items','categories'));
    }
}



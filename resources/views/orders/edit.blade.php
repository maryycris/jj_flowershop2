@extends('layouts.app')
 
@section('content')
    @include('orders.create', ['order' => $order, 'customers' => $customers, 'products' => $products])
@endsection 
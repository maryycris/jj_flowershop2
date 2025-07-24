@extends('layouts.app')
 
@section('content')
    @include('deliveries.create', ['delivery' => $delivery, 'orders' => $orders, 'drivers' => $drivers])
@endsection 
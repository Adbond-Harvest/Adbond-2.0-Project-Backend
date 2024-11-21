@extends('emails.layout')
    @section('title')
        Order Confirmation
    @endsection

    @section('heading')
        Order Confirmation
    @endsection
    
    @section('content')
        <div class="content">
            <h2>Dear {{ $order->client->full_name }}</h2>
            
            <p>Thank you for your order. We're excited to process your purchase!</p>
            
            <div class="order-details">
                <h3>Order Details</h3>
                <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                <p><strong>Package:</strong> {{ $order->package->name }}</p>
                <p><strong>Units:</strong> {{ $order->units }}</p>
                
                <p><strong>Total Amount:</strong> N{{ number_format($order->amount_payable, 2) }}</p>
                
                @if($order->is_installment)
                <p><strong>Payment Type:</strong> Installment</p>
                <!-- <p><strong>Next Payment Due:</strong> {{ $order->payment_due_date }}</p> -->
                @else
                <p><strong>Payment Type:</strong> Full Payment</p>
                @endif

                <h4>DISCOUNTS</h4>
                @foreach($order->discounts as $discount)
                    <b>{{ $discount->type }} Discount: </b>{{ $discount->discount }}%(N{{ number_format($discount->amount) }})
                @endforeach
            </div>
            
            <a href="{{ env('FRONTEND_URL') }}/orders/{{ $order->id }}" class="btn">View Order Details</a>
        </div>
        
    @endsection
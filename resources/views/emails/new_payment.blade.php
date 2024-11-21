@extends('emails.layout')
    @section('title')
        New Payment
    @endsection

    @section('heading')
        New Payment
    @endsection
    
    @section('content')
        
        <div class="content">
            <h2>Dear {{ $payment->client->full_name }}</h2>
            
            <p>We have received a new Payment from you!</p>
            
            <div class="order-details">
                You can find your payment receipt attached to this mail
            </div>
            
            <a href="{{ env('FRONTEND_URL') }}/payments/{{ $payment->id }}" class="btn">View Payment Details</a>
        </div>
        
    @endsection
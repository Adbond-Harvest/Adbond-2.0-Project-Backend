@extends('emails.layout')
    @section('title')
        New Payment
    @endsection

    @section('heading')
        New Payment
    @endsection
    
    @section('content')
        
        <div style="padding: 20px;">
            <h2>Dear {{ $payment->client->full_name }}</h2>
            
            <p>We have received a new Payment from you!</p>
            
            <div style="background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;">
                You can find your payment receipt attached to this mail
            </div>
        </div>
        
    @endsection
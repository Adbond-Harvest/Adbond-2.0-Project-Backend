@extends('emails.layout')
    @section('title')
        Purchase Contract
    @endsection

    @section('heading')
        Purchase Contract
    @endsection
    
    @section('content')
        
        <div style="padding: 20px;">
            <h2>Dear {{ $client->full_name }} your Contract of sale</h2>
            
            <p>Thank you for purchasing a property.</p>
            
            <div style="background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;">
                We’ve attached your Contract of sale to this email. Kindly download to view.
            </div>
        </div>
        
    @endsection


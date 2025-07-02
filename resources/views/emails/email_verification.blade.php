@extends('emails.layout')
    @section('title')
        Email Verification
    @endsection

    @section('heading')
        Email Verification
    @endsection
    
    @section('content')
        
        <div style="padding: 20px;">
            
            <p>Use the code below to verify your account</p>
            
            <div style="margin-top: 20px; margin-bottom: 20px; font-weight: bold;">
                {{$code}}
            </div>
            
            <p>Please note that this token will expire in the next 30mins</p>
        </div>
        
    @endsection


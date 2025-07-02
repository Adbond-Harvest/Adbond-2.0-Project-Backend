@extends('emails.layout')
    @section('title')
        Email Verification
    @endsection

    @section('heading')
        Email Verification
    @endsection
    
    @section('content')
        
        <div class="content">
            
            <p>Use the code below to verify your account</p>
            
            <div class="code">
                {{$code}}
            </div>
            
            <p>Please note that this token will expire in the next 30mins</p>
        </div>
        
    @endsection

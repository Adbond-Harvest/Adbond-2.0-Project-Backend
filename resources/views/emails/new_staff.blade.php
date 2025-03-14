@extends('emails.layout')
    @section('title')
        Staff Onboarding
    @endsection

    @section('heading')
        Staff Onboarding
    @endsection
    
    @section('content')
        <div class="content">
            <h2>Welcome {{ $user->name }}</h2>
            
            <p>You have been Registered as an Adbond Staff</p>
            
            <div class="order-details">
                <h3>Login Details</h3>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Password:</strong> {{ $password }}</p>
                
                <p><strong>Please endeavour to change your password to a more secure password after your login</strong></p>
                
            </div>
        </div>
        
    @endsection
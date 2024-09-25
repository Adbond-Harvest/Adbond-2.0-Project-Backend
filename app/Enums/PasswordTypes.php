<?php

namespace App\Enums;

enum PasswordTypes: string
    {
        case USER = 'user';
        case CLIENT = 'client';
    }
<?php

namespace app\Enums;

enum PasswordTypes: string
    {
        case USER = 'user';
        case CLIENT = 'client';
    }
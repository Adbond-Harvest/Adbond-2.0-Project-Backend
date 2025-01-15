<?php

namespace app\Enums;

enum UserType: string
    {
        case CLIENT = "app\Models\Client";
        case USER = "app\Models\User";
    }
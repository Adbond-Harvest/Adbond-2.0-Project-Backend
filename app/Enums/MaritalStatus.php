<?php

namespace App\Enums;

enum MaritalStatus: string
    {
        case SINGLE = 'single';
        case MARRIED = 'married';
        case WIDOW = 'widow';
        case WIDOWER = 'widower';
        case DIVORCED = 'divorced';
    }
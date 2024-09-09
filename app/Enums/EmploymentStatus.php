<?php

namespace App\Enums;

enum EmploymentStatus: string
    {
        case UNEMPLOYED = 'unemployed';
        case EMPLOYED = 'employed';
        case SELF_EMPLOYED = 'self-employed';
    }
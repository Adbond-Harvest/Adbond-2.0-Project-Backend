<?php

namespace App\Enums;

enum KYCStatus: string
    {
        case NOTSTARTED = 'not started';
        case STARTED = 'started';
        case COMPLETED = 'completed';
    }
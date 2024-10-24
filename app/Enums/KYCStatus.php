<?php

namespace app\Enums;

enum KYCStatus: string
    {
        case NOTSTARTED = 'not started';
        case STARTED = 'started';
        case COMPLETED = 'completed';
    }
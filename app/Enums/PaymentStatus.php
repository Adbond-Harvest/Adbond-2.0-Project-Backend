<?php

namespace app\Enums;

enum PaymentStatus: string
    {
        case PENDING = 'pending';
        case DEPOSIT = 'deposit';
        case COMPLETE = 'complete';
    }
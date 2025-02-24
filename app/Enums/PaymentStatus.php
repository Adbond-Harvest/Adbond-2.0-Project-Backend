<?php

namespace app\Enums;

enum PaymentStatus: string
    {
        case AWAITING_PAYMENT = 'awaiting payment';
        case PENDING = 'pending';
        case DEPOSIT = 'deposit';
        case COMPLETE = 'complete';
    }
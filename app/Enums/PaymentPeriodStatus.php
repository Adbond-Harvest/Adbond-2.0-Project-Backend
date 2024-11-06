<?php

namespace app\Enums;

enum PaymentPeriodStatus: string
    {
        case NORMAL = 'normal';
        case GRACE = 'grace';
        case PENALTY = 'penalty';
    }
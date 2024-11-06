<?php

namespace app\Enums;

enum DiscountType: string
    {
        case FULL_PAYMENT = 'full-payment';
        case LOYALTY = 'loyalty';
    }
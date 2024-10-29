<?php

namespace app\Enums;

enum OrderDiscountType: string
    {
        case FULL_PAYMENT = 'full-payment';
        case PROMO = 'promo';
    }
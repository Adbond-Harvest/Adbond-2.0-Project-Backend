<?php

namespace app\Enums;

enum PaymentMode: string
    {
        case CASH = 'cash';
        case BANK_TRANSFER = 'bank transfer';
        case CARD_PAYMENT = 'card payment';
    }
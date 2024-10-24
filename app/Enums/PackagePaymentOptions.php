<?php

namespace app\Enums;

enum PackagePaymentOption: string
    {
        case ONE_OFF = 'one-off';
        case INSTALLMENT = 'installment';
    }
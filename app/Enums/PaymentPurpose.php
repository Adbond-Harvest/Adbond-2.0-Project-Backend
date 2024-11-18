<?php

namespace app\Enums;

enum PaymentPurpose: string
    {
        case PACKAGE_FULL_PAYMENT = 'User Profile Photo';
        case INSTALLMENT_PAYMENT = 'Client Profile Photo';
    }
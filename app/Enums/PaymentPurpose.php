<?php

namespace app\Enums;

enum PaymentPurpose: string
    {
        case PACKAGE_FULL_PAYMENT = 'Full Payment';
        case INSTALLMENT_PAYMENT = 'Installment Payment';
        case OFFER_PAYMENT = 'Offer Payment';
    }
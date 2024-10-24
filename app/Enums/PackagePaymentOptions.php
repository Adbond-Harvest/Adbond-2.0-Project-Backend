<?php

namespace appEnums;

enum PackagePaymentOption: string
    {
        case ONE_OFF = 'one-off';
        case INSTALLMENT = 'installment';
    }
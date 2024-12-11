<?php

namespace app\Enums;

enum TransactionType: string
    {
        case IN_FLOW = 'in-flow';
        case OUT_FLOW = 'out-flow';
    }
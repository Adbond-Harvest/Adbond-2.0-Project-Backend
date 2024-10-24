<?php

namespace appEnums;

enum KYCStatus: string
    {
        case NOTSTARTED = 'not started';
        case STARTED = 'started';
        case COMPLETED = 'completed';
    }
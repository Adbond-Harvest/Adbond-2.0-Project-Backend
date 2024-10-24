<?php

namespace appEnums;

enum MaritalStatus: string
    {
        case SINGLE = 'single';
        case MARRIED = 'married';
        case WIDOW = 'widow';
        case WIDOWER = 'widower';
        case DIVORCED = 'divorced';
    }
<?php

namespace app\Enums;

enum Weekday: string
    {
        case MONDAY = "Monday";
        case TUESDAY = "Tuesday";
        case WEDNESDAY = "Wednesday";
        case THURSDAY = "Thursday";
        case FRIDAY = "Friday";
        case SATURDAY = "Saturday";
        case SUNDAY = "Sunday";
        case ALL = "All";
    }
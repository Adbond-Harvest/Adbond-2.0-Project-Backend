<?php

namespace app\Enums;

enum PostType: string
    {
        case NEWS = 'news';
        case EVENTS = 'events';
        case OFFERS = 'offers';
        case BLOG = 'blog';
        case PROMOTIONS = 'promotions';
    }
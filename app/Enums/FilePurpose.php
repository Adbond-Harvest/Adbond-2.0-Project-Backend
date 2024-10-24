<?php

namespace app\Enums;

enum FilePurpose: string
    {
        case USER_PROFILE_PHOTO = 'User Profile Photo';
        case CLIENT_PROFILE_PHOTO = 'Client Profile Photo';
        case PROJECT_TYPE_PHOTO = "Project Type Photo";
        case PROJECT_PHOTO = "Project Photo";
        case PACKAGE_PHOTO = "Package Photo";
        case PACKAGE_BROCHURE = "Package Brochure";
        
    }
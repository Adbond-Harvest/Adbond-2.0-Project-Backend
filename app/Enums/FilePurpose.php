<?php

namespace app\Enums;

enum FilePurpose: string
    {
        case USER_PROFILE_PHOTO = 'User Profile Photo';
        case CLIENT_PROFILE_PHOTO = 'Client Profile Photo';
        case PROJECT_TYPE_PHOTO = "Project Type Photo";
        case PROJECT_PHOTO = "Project Photo";
        case PACKAGE_PHOTO = "Package Photo";
        case PACKAGE_VIDEO = "Package Video";
        case PACKAGE_BROCHURE = "Package Brochure";
        case PAYMENT_EVIDENCE = "Payment Evidence";
        case PAYMENT_RECEIPT = "Payment Receipt";
        case CONTRACT = "contract";
        case LETTER_OF_HAPPINESS = "letter of happiness";
        case DEED_OF_ASSIGNMENT = "Deed of Assignment";
        case POST_MEDIA = "post media";
        
    }
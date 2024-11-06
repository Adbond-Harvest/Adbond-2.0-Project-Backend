<?php

namespace app;

use app\Enums\EmploymentStatus;
use app\Enums\MaritalStatus;
use app\Enums\KYCStatus;
use app\Enums\Genders;
use app\Enums\StaffTypes;
use app\Enums\FileTypes;
use app\Enums\FilePurpose;
use app\Enums\PaymentMode;

class EnumClass
{
    public static function employmentStatuses()
    {
        return [
            EmploymentStatus::EMPLOYED->value,
            EmploymentStatus::SELF_EMPLOYED->value,
            EmploymentStatus::UNEMPLOYED->value,
        ];   
    }

    public static function maritalStatus()
    {
        return [
            MaritalStatus::MARRIED->value,
            MaritalStatus::SINGLE->value,
            MaritalStatus::DIVORCED->value,
            MaritalStatus::WIDOW->value,
            MaritalStatus::WIDOWER->value
        ];
    }

    public static function kycStatus()
    {
        return [
            KYCStatus::NOTSTARTED->value,
            KYCStatus::STARTED->value,
            KYCStatus::COMPLETED->value
        ];
    }

    public static function genders()
    {
        return [
            Genders::FEMALE->value,
            Genders::MALE->value
        ];
    }

    public static function fileTypes()
    {
        return [
            FileTypes::CSV->value,
            FileTypes::DOC->value,
            FileTypes::DOCX->value,
            FileTypes::IMAGE->value,
            FileTypes::PDF->value,
            FileTypes::VIDEO->value,
            FileTypes::XLS->value
        ];
    }

    public static function filePurposes()
    {
        return [
            FilePurpose::USER_PROFILE_PHOTO->value,
            FilePurpose::CLIENT_PROFILE_PHOTO->value,
            FilePurpose::PROJECT_TYPE_PHOTO->value,
            FilePurpose::PROJECT_PHOTO->value,
            FilePurpose::PACKAGE_PHOTO->value,
            FilePurpose::PACKAGE_BROCHURE->value
        ];
    }

    public static function paymentModes()
    {
        return [
            PaymentMode::BANK_TRANSFER->value,
            PaymentMode::CARD_PAYMENT->value,
            PaymentMode::CASH->value
        ];
    }
}
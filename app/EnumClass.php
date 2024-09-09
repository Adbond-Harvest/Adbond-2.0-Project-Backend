<?php

namespace App;

use App\Enums\EmploymentStatus;
use App\Enums\MaritalStatus;
use App\Enums\KYCStatus;
use App\Enums\Genders;
use App\Enums\FileTypes;

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
}
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
use app\Enums\CommissionTransactionType;
use app\Enums\StaffCommissionType;
use app\Enums\ProductCategory;
use app\Enums\PostType;
use app\Enums\PackageType;
use app\Enums\InvestmentRedemptionOption;
use app\Enums\OfferApprovalStatus;

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

    public static function commissionTransactionTypes()
    {
        return [
            CommissionTransactionType::EARNING->value,
            CommissionTransactionType::REDEMPTION->value
        ];
    }

    public static function staffCommissionTypes()
    {
        return [
            StaffCommissionType::DIRECT->value,
            StaffCommissionType::INDIRECT->value
        ];
    }

    public static function ClientPackageFiles()
    {
        return [
            FilePurpose::CONTRACT->value,
            FilePurpose::DEED_OF_ASSIGNMENT->value,
            FilePurpose::LETTER_OF_HAPPINESS->value
        ];
    }

    // public static function productCategories()
    // {
    //     return [
    //         ProductCategory::PURCHASE->value,
    //         ProductCategory::INVESTMENT->value
    //     ];
    // }

    public static function postTypes()
    {
        return [
            PostType::BLOG->value,
            PostType::EVENTS->value,
            PostType::NEWS->value,
            PostType::OFFERS->value,
            PostType::PROMOTIONS->value
        ];
    }

    public static function packageTypes()
    {
        return [
            PackageType::INVESTMENT->value,
            PackageType::NON_INVESTMENT->value
        ];
    }

    public static function investmentRedemptionOptions()
    {
        return [
            InvestmentRedemptionOption::CASH->value,
            InvestmentRedemptionOption::PROFIT_ONLY->value,
            InvestmentRedemptionOption::PROPERTY->value
        ];
    } 

    public static function offerApprovalStatuses()
    {
        return [
            OfferApprovalStatus::PENDING->value,
            OfferApprovalStatus::APPROVED->value,
            OfferApprovalStatus::REJECTED->value
        ];
    }
}
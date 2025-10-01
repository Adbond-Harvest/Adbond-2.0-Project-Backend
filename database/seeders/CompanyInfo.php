<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use app\Models\CompanyInfo as Model;

class CompanyInfo extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = new Model;
        $company->name = "Adbond";
        $company->email = "contact@adbond.com";
        $company->about = "This is about Adbond";
        $company->year_founded = 2016;
        $company->virtual_staff_assessment_cut_off_mark = 60;
        $company->virtual_staff_assessment_time_limit = 60;
        $company->commission_tax = 5;
        $company->loyalty_discount = 10;
        $company->save();
    }
}

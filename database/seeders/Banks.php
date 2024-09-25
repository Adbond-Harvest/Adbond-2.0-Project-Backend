<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Bank;

class Banks extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banks = [
            "Access Bank Plc", "Advans La Fayette Microfinance", "Accion Microfinance Bank", "Citibank Nigeria", 
            "Coronation Merchant Bank", "Covenant Microfinance Bank",
            "Ecobank Nigeria", "Empire Trust Microfinance Bank", 
            "FairMoney Microfinance Bank", "FBNQuest Merchant Bank", "Fidelity Bank Plc", "Finca Microfinance Bank Limited", "Fina Trust Microfinance Bank", 
            "First City Monument Bank", 
            "First Bank of Nigeria Limited", "FSDH Merchant Bank", "Globus Bank", "Guaranty Trust Bank", 
            "Heritage Bank", "Infinity Microfinance Bank", "Jaiz Bank", "Keystone Bank", "Kuda Bank", "LOTUS BANK", 
            "Mint Finex MFB", "Mkobo MFB", "Moniepoint Microfinance Bank", "Mutual Trust Microfinance Bank", "Nova Merchant Bank",
            "Opay", "Optimus Bank", "Palmpay", "Parallex Bank Limited", "Peace Microfinance Bank", "Pearl Microfinance Bank",
            "Polaris Bank", "PremiumTrust Bank Limited", "Providus Bank Limited", "Rand Merchant Bank", "Raven bank", 
            "Rex Microfinance Bank",
            "Rephidim Microfinance Bank", "Rubies Bank",
            "Shepherd Trust Microfinance Bank", "Sparkle Bank", "Stanbic IBTC Bank", 
            "SunTrust Bank Nigeria Limited", "Standard Chartered", "Sterling Bank", "SunTrust Bank Nigeria", "TAJ Bank", 
            "Titan Trust bank", 
            "Union Bank of Nigeria", "United Bank for Africa", 
            "Unity Bank", "VFD Microfinance Bank", "Wema Bank", "Zenith Bank",
        ];

        foreach($banks as $bank) {
            Bank::firstOrCreate(['name'=>$bank]);
        }
    }
}

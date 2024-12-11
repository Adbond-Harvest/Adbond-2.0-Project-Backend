<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

use app\Models\ProjectType;
use app\Models\Client;
use app\Models\Project;
use app\Models\Package;
use app\Models\Order;
use app\Models\ClientPackage;
use app\Models\Payment;
use app\Models\PaymentStatus;

class InvestmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                "name" => "Package 1",
                "state" => "Lagos",
                "units" => 120,
                "available_units" => 120,
                "amount" => 1200000,
                "interest_return_duration" => 4,
                "interest_return_timeline" => 12,
                "interest_return_percentage" => 12,
            ],
            [
                "name" => "Package Fixed",
                "state" => "Lagos",
                "units" => 50,
                "available_units" => 50,
                "amount" => 1000000,
                "interest_return_duration" => 3,
                "interest_return_timeline" => 12,
                "interest_return_amount" => 150000,
            ],
            [
                "name" => "Emerald Package",
                "state" => "Jos",
                "units" => 80,
                "available_units" => 80,
                "amount" => 1500000,
                "interest_return_duration" => 4,
                "interest_return_timeline" => 18,
                "interest_return_percentage" => 10,
            ],
            [
                "name" => "Silver Package",
                "state" => "Enugu",
                "units" => 100,
                "available_units" => 100,
                "amount" => 2000000,
                "interest_return_duration" => 6,
                "interest_return_timeline" => 30,
                "interest_return_amount" => 300000,
            ]
        ];
        $projects = [
            [
                "name" => "Green-wide Agro-Investment",
                "project_type_id" => ProjectType::agro()->id,
                "packages" => [$packages[0], $packages[1]]
            ],
            [
                "name" => "Smart Homes Investment",
                "project_type_id" => ProjectType::homes()->id,
                "packages" => [$packages[2], $packages[3]]
            ]
        ];

        $clients = Client::all();
        $clientsIds = [];
        $selectedClientIds = [];

        if($clients->count() > 0) {
            foreach($clients as $client) {
                $clientsIds[] = $client->id;
            }
        }

        $totalSelection = ($clients->count()/2 < 4) ? $clients->count() : $clients->count()/2;
        if($clients->count() > $totalSelection) {
            for($i=$totalSelection; $i>0; $i--) {
                do{
                    $j = rand(0, count($clientsIds) -1);
                }while(in_array($j, $selectedClientIds));
                $selectedClientIds[] = $j; 
            }
        }else{
            $selectedClientIds = $clientsIds;
        }

        $packages = [];

        foreach($projects as $project) {
            $projectObj = new Project;
            $projectObj->name = $project['name'];
            $projectObj->project_type_id = $project['project_type_id'];
            $projectObj->save();

            foreach($project['packages'] as $package) {
                $packageObj = new Package;
                $packageObj->name = $package['name'];
                $packageObj->state = $package['state'];
                $packageObj->units = $package['units'];
                $packageObj->available_units = $package['available_units'];
                $packageObj->amount = $package['amount'];
                $packageObj->interest_return_duration = $package['interest_return_duration'];
                $packageObj->interest_return_timeline = $package['interest_return_timeline'];
                if(isset($package['interest_return_amount'])) $packageObj->interest_return_amount = $package['interest_return_amount'];
                if(isset($package['interest_return_percentage'])) $packageObj->interest_return_percentage = $package['interest_return_percentage'];
                $packageObj->save();
                $packages[] = $packageObj;
            }
        }

        foreach($selectedClientIds as $clientId) {
            $package = $packages[rand(0, count($packages))];
            $order = new Order;
            $order->client_id = $clientId;
            $order->units = 2;
            $order->amount_payed = $order->units * $package->amount;
            $order->amount_payable = $order->units * $package->amount;
            $order->balance = 0;
            $order->payment_status_id = PaymentStatus::pending()->id;
        }

    }

    /*
        $table->foreignId("client_id")->references("id")->on("clients");
            $table->foreignId("package_id")->references("id")->on("packages");
            $table->double("units");
            $table->double("amount_payed");
            $table->double("amount_payable");
            $table->foreignId("promo_code_id")->nullable()->references("id")->on("promo_codes");
            $table->boolean("is_installment")->default(false);
            $table->double("balance");
            $table->foreignId("payment_status_id");
            $table->date("order_date");
            $table->date("payment_due_date")->nullable();
            $table->date("grace_period_end_date")->nullable();
            $table->date("penalty_period_end_date")->nullable();
            $table->foreignId("payment_period_status_id");
    */
}

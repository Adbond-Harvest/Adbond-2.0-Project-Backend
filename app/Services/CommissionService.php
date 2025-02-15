<?php

namespace app\Services;

use Illuminate\Support\Facades\DB;

use app\Notifications\APIPasswordResetNotification;
use app\Exceptions\UserNotFoundException;

use app\Models\Payment;
use app\Models\Payment_mode;
use app\Models\StaffCommissionEarning;
use app\Models\DeductibleFee;
use app\Models\CommissionRate;
use app\Models\ClientCommissionEarning;
use app\Models\ClientCommissionRate;

use app\Enums\StaffTypes;
use app\Enums\StaffCommissionType;

use app\Helpers;
use app\Utilities;
/**
 * Commission service class
 */
class CommissionService
{

    public function setCommissionRate($data)
    {
        $installmentCommissionRate = CommissionRate::where('staff_type_id', $data['staff_type_id'])->where('installment', 1)->first();
        $fullpaymentCommissionRate = CommissionRate::where('staff_type_id', $data['staff_type_id'])->where('installment', 0)->first();
        $newCommissionRate = new CommissionRate;
        if($installmentCommissionRate) {
            // dd('here1');
           $installmentCommissionRate->direct = $data['installment']['direct'];
           $installmentCommissionRate->indirect = $data['installment']['indirect']; 
           $installmentCommissionRate->update();
        }else{
            // dd('here2');
            $newCommissionRate = new CommissionRate;
            $newCommissionRate->staff_type_id = $data['staff_type_id'];
            $newCommissionRate->installment = 1;
            $newCommissionRate->direct = $data['installment']['direct'];
            $newCommissionRate->indirect = $data['installment']['indirect'];
            $newCommissionRate->save();
        }
        if($fullpaymentCommissionRate) {
            // dd('here3');
            $fullpaymentCommissionRate->direct = $data['fullpayment']['direct'];
            $fullpaymentCommissionRate->indirect = $data['fullpayment']['indirect']; 
            $fullpaymentCommissionRate->update();
        }else{
            // dd('here4');
            $newCommissionRate = new CommissionRate;
            $newCommissionRate->staff_type_id = $data['staff_type_id'];
            $newCommissionRate->installment = 0;
            $newCommissionRate->direct = $data['fullpayment']['direct'];
            $newCommissionRate->indirect = $data['fullpayment']['indirect'];
            $newCommissionRate->save();
         }
         return CommissionRate::where('staff_type_id', $data['staff_type_id'])->get();
    }

    public function setClientCommissionRate($rate)
    {
        $clientRate = ClientCommissionRate::first() ?? new ClientCommissionRate;
        $clientRate->rate = $rate;
        $clientRate->save();

        return $clientRate;
    }

    public function saveClientEarning($client, $order)
    {
        $fee = DeductibleFee::where("name", "commission tax")->first();
        $commissionTax = $fee ? $fee->commission_tax : 0;
        $clientCommissionRate = ClientCommissionRate::first();

        if($clientCommissionRate) {
            $clientCommission = new ClientCommissionEarning;
            $clientCommission->client_id = $client->id;
            $clientCommission->order_id = $order->id;
            $clientCommission->amount = $order->amount_payable;
            $clientCommission->commission = $clientCommissionRate->rate;
            $clientCommission->commission_amount = round(($clientCommissionRate->rate/100) * $clientCommission->amount, 2);
            $clientCommission->tax = $commissionTax;
            $taxAmount = round(($commissionTax/100) * $clientCommission->commission_amount, 2);
            $clientCommission->amount_after_tax = $clientCommission->commission_amount - $taxAmount;
            $clientCommission->save();

            return $clientCommission;
        }

        /*
            $table->foreignId("client_id");
            $table->foreignId("order_id");
            $table->double("amount");
            $table->double("commission");
            $table->double("commission_amount");
            $table->double("tax");
            $table->double("amount_after_tax");
        */
    }

    public function save($user, $order)
    {
        if($user->staffType && $user->staffType->name=="e-staff") {
            $staff = $user->registerer;
        }
        $commission = $this->addDirectCommission($user, $order);
        if(isset($staff) && $staff != null) $this->addIndirectCommission($staff, $order);
        return true;
    }

    // public function completeCommissionPayment($user, $commission)
    // {

    //     $commissionPayment = new UserCommissionPayment;
    //     $commissionPayment->user_id = $user->id;
    //     $commissionPayment->user_commission_id = $commission->id;
    //     $commissionPayment->amount = $commission->balance;
    //     $commissionPayment->balance = 0;
    //     $commissionPayment->save();

    //     $commission->balance = 0;
    //     $commission->update();

    //     $this->updateUserCommission($user, $commissionPayment);

    //     return $commission;
    // }

    // public function getUserPayments($user_id)
    // {
    //     return UserCommissionPayment::where('user_id', $user_id)->get();
    // }

    // public function getLatestUserPayments($user_id)
    // {
    //     return UserCommissionPayment::where('user_id', $user_id)->orderBy('created_at', 'desc')->first();
    // }

    // public function getTotalUserCommissionBalance($user_id)
    // {
    //     return UserCommission::select(DB::raw("SUM(balance) as total"))->where('user_id', $user_id)->get();
    // }

    // public function getUserCommissionByOrderId($order_id)
    // {
    //     return UserCommission::where('order_id', $order_id)->first();
    // }

    // public function getUserCommissionsByMonth($year, $start, $end=null, $page=null, $perPage=null)
    // {
    //     if($page != null) {
    //         if($page <= 0) $page = 1;
    //         $offset = $perPage * ($page-1);
    //     }
    //     $query = ($end != null) ? UserCommissionPayment::whereYear('created_at', $year)->whereMonth('created_at', '>=', $start)->whereMonth('created_at', '<=', $end) 
    //                                 : 
    //                                 UserCommissionPayment::whereMonth('created_at', '=', $start);
    //     return $query->limit($perPage)->offset($offset)->orderBy('created_at', 'desc')->get();
    // }

    // public function getUserCommissionsByMonthCount($year, $start, $end=null)
    // {
    //     $query = ($end != null) ? UserCommissionPayment::whereYear('created_at', $year)->whereMonth('created_at', '>=', $start)->whereMonth('created_at', '<=', $end) 
    //                                 : 
    //                                 UserCommissionPayment::whereMonth('created_at', '=', $start);
    //     return $query->count();
    // }

    // public function totalPayments($user_id)
    // {
    //     $total = 0;
    //     $userPayments = $this->getUserPayments($user_id);
    //     if($userPayments->count() > 0) {
    //         foreach($userPayments as $userPayment) {
    //             $total += $userPayment->amount;
    //         }
    //     }
    //     return $total;
    // }

    private function taxCommission($commissionPercentage, $amount) 
    {
        $tax = DeductibleFee::commissionTax()->percentage;
        $commissionBeforeTax = (float)($commissionPercentage/100) * (float)$amount;
        $commissionTax = (float)$commissionBeforeTax * ((float)$tax/100);
        $commissionAmount = (float)$commissionBeforeTax - (float)$commissionTax;
        return [
            "beforeTax" => $commissionBeforeTax,
            "afterTax" => $commissionAmount,
            "tax" => $tax
        ];
    }

    private function addDirectCommission($user, $order)
    {
        $staffType = $user->staffType;
        $amount = $order->amount_payable;
        $commissionPercentage = ($order->installment == 0) ? $this->getCommissionPercentage($staffType) : $this->getCommissionInstallmentPercentage($staffType);
        // $commissionBeforeTax = (float)($commissionPercentage/100) * (float)$amount;
        // $commissionTax = (float)$commissionBeforeTax * ((float)$tax/100);
        // $commissionAmount = (float)$commissionBeforeTax - (float)$commissionTax;

        $taxCommission = $this->taxCommission($commissionPercentage, $amount);

        $commission = new StaffCommissionEarning;

        $commission->user_id = $user->id;
        $commission->order_id = $order->id;
        $commission->amount = $amount;
        $commission->tax = $taxCommission['tax'];
        $commission->commission_amount = $taxCommission['beforeTax'];
        $commission->commission = $commissionPercentage;
        $commission->commission_after_tax = (float)$taxCommission['afterTax'];
        $commission->type = StaffCommissionType::DIRECT->value;
        $commission->save();

        return $commission;
    }

    private function addIndirectCommission($user, $order)
    {
        $staffType = ($user->staffType) ? $user->staffType : null;
        $amount = $order->amount;
        $commissionPercentage = ($order->installment == 0) ? $this->getCommissionPercentage($staffType, 0) : $this->getCommissionInstallmentPercentage($staffType, 0);

        $taxCommission = $this->taxCommission($commissionPercentage, $amount);

        $commission = new StaffCommissionEarning;

        $commission->user_id = $user->id;
        $commission->order_id = $order->id;
        $commission->amount = $amount;
        $commission->tax = $taxCommission['tax'];
        $commission->commission_amount = $taxCommission['beforeTax'];
        $commission->commission = $commissionPercentage;
        $commission->commission_after_tax = (float)$taxCommission['afterTax'];
        $commission->type = StaffCommissionType::DIRECT->value;
        $commission->save();

        return $commission;
    }

    private function getCommissionPercentage($staffType, $direct=1)
    {
        $commission = 0;
        if($staffType) {
            if($direct == 1) {
                $commissionRate = CommissionRate::where('staff_type_id', $staffType->id)->where('installment', 0)->first();
                $commission = $commissionRate->direct;
            }else{
                $commissionRate = CommissionRate::where('staff_type_id', $staffType->id)->where('installment', 0)->first();
                $commission = $commissionRate->indirect;
            }
        }
        return $commission;
    }

    private function getCommissionInstallmentPercentage($staffType, $direct=1)
    {
        $commission = 0;
        if($staffType) {
            if($direct == 1) {
                $commissionRate = CommissionRate::where('staff_type_id', $staffType->id)->where('installment', 1)->first();
                $commission = $commissionRate->direct;
            }else{
                $commissionRate = CommissionRate::where('staff_type_id', $staffType->id)->where('installment', 1)->first();
                $commission = $commissionRate->indirect;
            }
        }
        return $commission/2;
    }

}
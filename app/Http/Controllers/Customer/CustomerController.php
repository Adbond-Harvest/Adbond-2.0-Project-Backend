<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\customer\AddNextOfKin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Resources\CustomerBriefResource;
use App\Http\Resources\CustomerNextOfKinResource;

use App\Http\Requests\Customer\UpdateCustomer;

use App\Services\CustomerService;

use App\Utilities;

class CustomerController extends Controller
{
    private $customerService;

    public function __construct()
    {
        $this->customerService = new CustomerService;
    }

    public function update(UpdateCustomer $request)
    {
        try{
            $data = $request->validated();
            if(count($data) == 0) return Utilities::error402(' enter at least one valid field');
            $customer = $this->customerService->update($data, Auth::guard('customer')->user());
            return new CustomerBriefResource($customer);
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occured while trying to send verification mail, Please try again later or contact support');
        }
    }

    public function addNextOfKin(AddNextOfKin $request)
    {
        try{
            $data = $request->validated();
            $data['customerId'] = Auth::guard('customer')->user()->id;
            $kin = $this->customerService->updateNextOfKin($data);
            return Utilities::okay(new CustomerNextOfKinResource($kin));
        }catch(\Exception $e){
            return Utilities::error($e, 'An error occured while trying to send verification mail, Please try again later or contact support');
        }
    }
}

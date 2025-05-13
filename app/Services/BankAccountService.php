<?php

namespace app\Services;

use app\Models\BankAccount;
use app\Models\UserBankAccount;

class BankAccountService
{

    public function save($data, $company=true)
    {
        $account = ($company) ? new BankAccount : new UserBankAccount;
        $account->name = $data['name'];
        $account->number = $data['number'];
        $account->bank_id = $data['bankId'];
        if(!$company) $account->user_id = $data['userId'];

        $account->save();

        return $account;
    }

    public function update($data, $account)
    {
        if(isset($data['name'])) $account->name = $data['name'];
        if(isset($data['number'])) $account->number = $data['number'];
        if(isset($data['bankId'])) $account->bank_id = $data['bankId'];

        $account->update();

        return $account;
    }

    public function account($id, $with=[], $company=true)
    {
        $query = ($company) ? BankAccount::with($with) : UserBankAccount::with($with);
        return $query->where("id", $id)->first();
    }

    public function accounts($with=[])
    {
        return BankAccount::with($with)->orderBy("created_at", "DESC")->get();
    }

    public function userAccounts($userId, $with=[])
    {
        return UserBankAccount::with($with)->where("user_id", $userId)->orderBy("created_at", "DESC")->get();
    }

    public function activate($account)
    {
        $account->active = 1;
        $account->update();
        return $account;
    }

    public function deactivate($account)
    {
        $account->active = 0;
        $account->update();
        return $account;
    }

    public function delete($account)
    {
        $account->delete();
    }
}
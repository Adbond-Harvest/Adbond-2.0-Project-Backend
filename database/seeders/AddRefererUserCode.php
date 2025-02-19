<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use app\Models\User;

use app\Utilities;

use app\Enums\RefererCodePrefix;
use app\Enums\UserType;

class AddRefererUserCode extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if($users->count() > 0) {
            foreach($users as $user) {
                if(strpos(!$user->referer_code || $user->referer_code, RefererCodePrefix::USER->value) == false) {
                    $refererCode = ($user->referer_code) ? RefererCodePrefix::USER->value.$user->referer_code : Utilities::generateRefererCode(UserType::USER->value);
                    $user->referer_code = $refererCode;
                    $user->update();
                }
            }
        }
    }
}

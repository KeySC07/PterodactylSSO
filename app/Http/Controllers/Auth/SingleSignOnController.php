<?php

namespace Pterodactyl\Http\Controllers\Auth;

use Laravel\Socialite\Facades\Socialite;
use Pterodactyl\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

class SingleSignOnController extends AbstractLoginController
{

    public function Driver()
    {
        return Socialite::driver('authentik')->redirect();
    }

    public function DriverCallback()
    {
        $service = Socialite::driver('authentik')->user();

        if (User::where('email', '=', $service->getEmail())->exists()) {
            $getUser = User::where('email', $service->getEmail())->first();
            Auth::loginUsingId($getUser->id);
        } else {
            if(config('sso.create_account')) {
                $this->CreateNewAccount($service);
            }
        }

        return redirect('/');
    }

    private function CreateNewAccount($service)
    {
        $email = $service->getEmail();
        if(!$service->getNickname()) { $username =  $service->getName(); } else { $username =  $service->getNickname(); }
        
        $username = str_replace(' ', '', $username);
        $username = preg_replace("/[^a-zA-Z0-9]+/", "", $username);


        //TODO Add admin auth based on user groups: $userGroups = $service->groups;

        $res = Artisan::call("p:user:make --email={$email} --username={$username} --name-first={$username} --name-last={$username} --admin=0 --no-password");
        $getUser = User::where('email', $service->getEmail())->first();
        Auth::loginUsingId($getUser->id);

    }


}
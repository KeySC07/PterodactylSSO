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
        $username = !$service->getNickname() ? $service->getName() : $service->getNickname();
        
        // Normalize username by removing spaces and non-alphanumeric characters
        $username = str_replace(' ', '', $username);
        $username = preg_replace("/[^a-zA-Z0-9]+/", "", $username);
    
        // Assuming the OAuth token includes attributes for groups, first name, and last name
        // You'll need to replace 'groups', 'firstName', and 'lastName' with the actual attribute names
        $userGroups = $service->groups;
        list($firstName, $lastName) = explode(' ', $service->getName(), 2);
    
        // Determine if the user should be an admin based on their groups
        $isAdmin = 0; // Default to non-admin
        if (in_array('Pterodactyl Admins', $userGroups)) { // Adjust 'admin' to match the actual admin group name
            $isAdmin = 1;
        }
    
        // Replace "--name-first={$username} --name-last={$username}" with actual first name and last name
        // and "--admin=0" with "$isAdmin" to set admin rights based on group membership
        $res = Artisan::call("p:user:make --email={$email} --username={$username} --name-first={$firstName} --name-last={$lastName} --admin={$isAdmin} --no-password");
    
        $getUser = User::where('email', $email)->first();
        Auth::loginUsingId($getUser->id);
    }


}
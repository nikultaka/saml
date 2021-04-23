<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Aacotroneo\Saml2\Events\Saml2LoginEvent;
use App\Providers\Exception;
use App\Models\User;
use Auth;
use Session;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];
    public function boot()
    {
        try{
            Event::listen('Aacotroneo\Saml2\Events\Saml2LoginEvent', function (Saml2LoginEvent $event) {
                $messageId = $event->getSaml2Auth()->getLastMessageId();
                $user = $event->getSaml2User();

                $userData = [
                    'id' => $user->getUserId(),
                    'attributes' => $user->getAttributes(),
                    'assertion' => $user->getRawSamlAssertion()
                ];

                // echo '<pre>'; 
                // print_r($userData); 
                // exit;

                $userInfo = User::where('email',$userData['id'])->first();
                if($userInfo) {
                    //$loggedInUser = Auth::loginUsingId($userInfo->id);
                    request()->session()->invalidate();
                    request()->session()->regenerateToken();
                    return Auth::login($userInfo);
                }

            });
        } catch (\Throwable $e) {
            //echo $e->getMessage(); die;
            //report($e);
        }
        
    }
}

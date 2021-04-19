<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Aacotroneo\Saml2\Events\Saml2LoginEvent;
use App\Providers\Exception;
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        
        try{
            Event::listen('Aacotroneo\Saml2\Events\Saml2LoginEvent', function (Saml2LoginEvent $event) {
                $messageId = $event->getSaml2Auth()->getLastMessageId();
                // Add your own code preventing reuse of a $messageId to stop replay attacks
            
                $user = $event->getSaml2User();
                dd($user);
                $userData = [
                    'id' => $user->getUserId(),
                    'attributes' => $user->getAttributes(),
                    'assertion' => $user->getRawSamlAssertion()
                ];
                dd($userData);
                    // $laravelUser = //find user by ID or attribute
                    // //if it does not exist create it and go on  or show an error message
                    // Auth::login($laravelUser);
            });
        } catch (\Throwable $e) {
            echo $e->getMessage(); die;
            report($e);
    
        }
        
    }
}

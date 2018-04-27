<?php

namespace Cerpus\QuestionBankClient\Providers;


use Cerpus\QuestionBankClient\Clients\Client;
use Cerpus\QuestionBankClient\Clients\Oauth1Client;
use Cerpus\QuestionBankClient\Clients\Oauth2Client;
use Cerpus\QuestionBankClient\Contracts\QuestionBankClientContract;
use Cerpus\QuestionBankClient\Contracts\QuestionBankContract;
use Cerpus\QuestionBankClient\QuestionBankClient;
use Cerpus\QuestionBankClient\DataObjects\OauthSetup;
use Illuminate\Support\ServiceProvider;

class QuestionBankClientServiceProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind(QuestionBankClientContract::class, function ($app) {
            $questionbankClient = $app['config']->get("questionbank-client");
            $client = strtolower($questionbankClient['adapter']['client']);
            /** @var QuestionBankClientContract $clientClass */
            switch ($client) {
                case "oauth1":
                    $clientClass = Oauth1Client::class;
                    break;
                case "oauth2":
                    $clientClass = Oauth2Client::class;
                    break;
                default:
                    $clientClass = Client::class;
                    break;
            }

            return $clientClass::getClient(OauthSetup::create([
                'coreUrl' => $questionbankClient['core']['url'],
                'key' => $questionbankClient['core']['key'],
                'secret' => $questionbankClient['core']['secret'],
                'authUrl' => $questionbankClient['auth']['url'],
                'token' => $questionbankClient['core']['token'],
                'token_secret' => $questionbankClient['core']['token_secret'],
            ]));
        });

        $this->app->bind(QuestionBankContract::class, function ($app) {
            $questionbankClient = $app['config']->get("questionbank-client");
            $client = $app->make(QuestionBankClientContract::class);
            return new $questionbankClient['adapter']['current']($client);
        });

        $this->mergeConfigFrom(QuestionBankClient::getConfigPath(), QuestionBankClient::$alias);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [QuestionBankContract::class];
    }

}
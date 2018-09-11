<?php

namespace Cerpus\QuestionBankClient\Providers;


use Cerpus\QuestionBankClient\Clients\Client;
use Cerpus\QuestionBankClient\Clients\Oauth1Client;
use Cerpus\QuestionBankClient\Clients\Oauth2Client;
use Cerpus\QuestionBankClient\Contracts\QuestionBankClientContract;
use Cerpus\QuestionBankClient\Contracts\QuestionBankContract;
use Cerpus\QuestionBankClient\Exceptions\InvalidConfigException;
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
            $questionbankClientConfig = $app['config']->get(QuestionBankClient::$alias);
            $adapter = $questionbankClientConfig['default'];

            $this->checkConfig($questionbankClientConfig, $adapter);

            $adapterConfig = array_merge($this->getDefaultClientStructure(), $questionbankClientConfig["adapters"][$adapter]);
            $client = strtolower($adapterConfig['auth-client']);
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
                'baseUrl' => $adapterConfig['base-url'],
                'authUrl' => $adapterConfig['auth-url'],
                'authUser' => $adapterConfig['auth-user'],
                'authSecret' => $adapterConfig['auth-secret'],
                'authToken' => $adapterConfig['auth-token'],
                'authTokenSecret' => $adapterConfig['auth-token_secret'],
            ]));
        });

        $this->app->bind(QuestionBankContract::class, function ($app) {
            $client = $app->make(QuestionBankClientContract::class);
            $questionbankClientConfig = $app['config']->get(QuestionBankClient::$alias);
            $adapter = $questionbankClientConfig['default'];

            $this->checkConfig($questionbankClientConfig, $adapter);

            $adapterConfig = $questionbankClientConfig["adapters"][$adapter];
            return new $adapterConfig['handler']($client);
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
        return [
            QuestionBankContract::class,
            QuestionBankClientContract::class,
        ];
    }

    private function getDefaultClientStructure()
    {
        return [
            "handler" => null,
            "base-url" => "",
            "auth-client" => "none",
            "auth-url" => "",
            "auth-user" => "",
            "auth-secret" => "",
            "auth-token" => "",
            "auth-token_secret" => "",
            "concurrency" => 50,
        ];
    }

    private function checkConfig($config, $adapter)
    {
        if (!array_key_exists($adapter, $config['adapters']) || !is_array($config['adapters'][$adapter])) {
            throw new InvalidConfigException(sprintf("Could not find the config for the adapter '%s'", $adapter));
        }
    }
}

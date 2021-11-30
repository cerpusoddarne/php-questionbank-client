<?php

namespace Cerpus\QuestionBankClient\Providers;


use Cerpus\Helper\DataObjects\OauthSetup;
use Cerpus\QuestionBankClient\Clients\Client;
use Cerpus\QuestionBankClient\Contracts\QuestionBankClientContract;
use Cerpus\QuestionBankClient\Contracts\QuestionBankContract;
use Cerpus\QuestionBankClient\Exceptions\InvalidConfigException;
use Cerpus\QuestionBankClient\QuestionBankClient;
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

            $adapterConfig = array_merge($this->getDefaultClientStructure(),
                $questionbankClientConfig["adapters"][$adapter]);

            return Client::getClient(OauthSetup::create([
                'coreUrl' => $adapterConfig['base-url'],
                'key'     => $adapterConfig['auth-user'],
                'secret'  => $adapterConfig['auth-secret'],
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
            "base-url"    => "",
            "auth-user"   => "",
            "auth-secret" => "",
        ];
    }

    /**
     * @param $config
     * @param $adapter
     *
     * @throws InvalidConfigException
     */
    private function checkConfig($config, $adapter)
    {
        if (!array_key_exists($adapter, $config['adapters']) || !is_array($config['adapters'][$adapter])) {
            throw new InvalidConfigException(sprintf("Could not find the config for the adapter '%s'", $adapter));
        }
    }
}

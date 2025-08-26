<?php
namespace App\Payment\Provider;

use App\Payment\Contract\PaymentProviderInterface;

class ProviderRegistry
{
    /** @var iterable<PaymentProviderInterface> */
    private iterable $providers;

    public function __construct(iterable $providers = [])
    {
        $this->providers = $providers;
    }

    public function get(string $providerName): PaymentProviderInterface
    {
        foreach ($this->providers as $provider) {
            if ($provider->getName() === $providerName) {
                return $provider;
            }
        }

        throw new \RuntimeException("No provider found for '$providerName'");
    }

    public function getProviders(): iterable
    {
        return $this->providers;
    }
}

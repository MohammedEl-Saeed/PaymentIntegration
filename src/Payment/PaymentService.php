<?php
namespace App\Payment;

use App\Payment\Dto\ChargeRequest;
use App\Payment\Dto\ChargeResponse;
use App\Payment\Dto\RefundRequest;
use App\Payment\Dto\RefundResponse;
use App\Payment\Provider\ProviderRegistry;

class PaymentService
{
    public function __construct(
        private ProviderRegistry $registry,
        private string $defaultProvider = 'shift4'
    ) {}

    public function charge(ChargeRequest $request, ?string $provider = null): ChargeResponse
    {
        return $this->registry->get($provider ?? $this->defaultProvider)->charge($request);
    }

    public function refund(RefundRequest $request, ?string $provider = null): RefundResponse
    {
        return $this->registry->get($provider ?? $this->defaultProvider)->refund($request);
    }
}

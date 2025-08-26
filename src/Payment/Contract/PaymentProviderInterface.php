<?php
namespace App\Payment\Contract;

use App\Payment\Dto\ChargeRequest;
use App\Payment\Dto\ChargeResponse;
use App\Payment\Dto\RefundRequest;
use App\Payment\Dto\RefundResponse;

interface PaymentProviderInterface
{
    public function getName(): string; // e.g. 'shift4'

    /** @throws \RuntimeException on provider/network errors */
    public function charge(ChargeRequest $request): ChargeResponse;

    /** Optional bonus */
    public function refund(RefundRequest $request): RefundResponse;
}

<?php
namespace App\Payment\Dto;

class ChargeResponse
{
    public function __construct(
        public string $transactionId,
        public string $status,   // 'success' | 'failed'
        public float  $amount,
        public string $currency,
        public ?string $message = null // error details when failed
    ) {}
}

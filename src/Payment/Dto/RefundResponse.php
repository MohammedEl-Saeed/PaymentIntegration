<?php
namespace App\Payment\Dto;

class RefundResponse
{
    public function __construct(
        public string $refundId,
        public string $status,      // 'success' | 'failed'
        public ?string $message=null
    ) {}
}

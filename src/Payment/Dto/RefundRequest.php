<?php
namespace App\Payment\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class RefundRequest
{
    #[Assert\NotBlank]
    public string $transactionId;

    #[Assert\Positive]
    public ?float $amount = null;

    public function __construct(array $data)
    {
        $this->transactionId = $data['transactionId'] ?? '';
        $this->amount   = isset($data['amount']) ? (float) $data['amount'] : null;
    }

}

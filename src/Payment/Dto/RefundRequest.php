<?php
namespace App\Payment\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class RefundRequest
{
    #[Assert\NotBlank]
    public string $chargeId;

    #[Assert\Positive]
    public ?float $amount = null;

    public function __construct(array $data)
    {
        $this->chargeId = $data['chargeId'] ?? '';
        $this->amount   = isset($data['amount']) ? (float) $data['amount'] : null;
    }

}

<?php
namespace App\Payment\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ChargeRequest
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public float $amount;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 3)]
    public string $currency;

    #[Assert\Valid]
    public CardDto $card;

    public function __construct(array $data)
    {
        $this->amount   = (float) ($data['amount'] ?? 0);
        $this->currency = $data['currency'] ?? '';
        
        $cardData = $data['card'] ?? [];
        $this->card = new CardDto(
            $cardData['number'] ?? '',
            $cardData['expMonth'] ?? 0,
            $cardData['expYear'] ?? 2025,
            $cardData['cvv'] ?? ''
        );
    }
}

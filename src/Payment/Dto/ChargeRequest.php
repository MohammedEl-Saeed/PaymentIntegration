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
            $cardData['expMonth'] ?? $cardData['exp_month'] ?? 0,
            $cardData['expYear'] ?? $cardData['exp_year'] ?? 0,
            $cardData['cvv'] ?? $cardData['cvc'] ?? ''
        );
    }
}

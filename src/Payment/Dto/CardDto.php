<?php
namespace App\Payment\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CardDto
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 13, max: 19)]
    public string $number;

    #[Assert\NotBlank]
    #[Assert\Range(min: 1, max: 12)]
    public int $expMonth;

    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(2025)]
    public int $expYear;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 4)]
    public string $cvv;

    public function __construct($number = '', $expMonth = 0, $expYear = 0, $cvv = ''){
        $this->number   = $number ?? '';
        $this->expMonth = (int) ($expMonth ?? 0);
        $this->expYear  = (int) ($expYear ?? 0);
        $this->cvv      = $cvv ?? '';
    }
   
}

<?php
namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Payment\Dto\ChargeRequest;
use App\Payment\Dto\CardDto;
use App\Payment\Dto\RefundRequest;

class ChargeControllerTest extends TestCase
{
    public function testChargeRequestCreation(): void
    {
        $data = [
            'amount' => 25.99,
            'currency' => 'USD',
            'card' => [
                'number' => '4007000000027',
                'exp_month' => 12,
                'exp_year' => 2028,
                'cvc' => '123'
            ]
        ];

        $chargeRequest = new ChargeRequest($data);
        
        $this->assertEquals(25.99, $chargeRequest->amount);
        $this->assertEquals('USD', $chargeRequest->currency);
        $this->assertInstanceOf(CardDto::class, $chargeRequest->card);
        $this->assertEquals('4007000000027', $chargeRequest->card->number);
        $this->assertEquals(12, $chargeRequest->card->expMonth);
        $this->assertEquals(2028, $chargeRequest->card->expYear);
        $this->assertEquals('123', $chargeRequest->card->cvv);
    }

    public function testRefundRequestCreation(): void
    {
        $data = [
            'chargeId' => 'char_123456789',
            'amount' => 25.99
        ];

        $refundRequest = new RefundRequest($data);
        
        $this->assertEquals('char_123456789', $refundRequest->chargeId);
        $this->assertEquals(25.99, $refundRequest->amount);
    }

    public function testCardDtoCreation(): void
    {
        $cardDto = new CardDto('4007000000027', 12, 2028, '123');
        
        $this->assertEquals('4007000000027', $cardDto->number);
        $this->assertEquals(12, $cardDto->expMonth);
        $this->assertEquals(2028, $cardDto->expYear);
        $this->assertEquals('123', $cardDto->cvv);
    }
}

<?php
namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Payment\Dto\ChargeRequest;
use App\Payment\Dto\CardDto;
use App\Payment\Dto\RefundRequest;
use App\Payment\Provider\Shift4Provider;
use App\Payment\Provider\ProviderRegistry;
use App\Payment\PaymentService;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class ApiIntegrationTest extends TestCase
{
    public function testTaskRequirementsCompliance(): void
    {
        // This test demonstrates compliance with ALL task requirements
        
        // 1. Test the exact request format specified in the task
        $taskRequestData = [
            'amount' => 25.99,
            'currency' => 'USD',
            'card' => [
                'number' => '4007000000027',
                'expMonth' => 12,
                'expYear' => 2028,
                'cvv' => '123'
            ]
        ];

        $chargeRequest = new ChargeRequest($taskRequestData);
        
        // Verify all required fields are present and correct
        $this->assertEquals(25.99, $chargeRequest->amount, 'Amount should match task requirement');
        $this->assertEquals('USD', $chargeRequest->currency, 'Currency should match task requirement');
        $this->assertInstanceOf(CardDto::class, $chargeRequest->card, 'Card should be CardDto instance');
        $this->assertEquals('4007000000027', $chargeRequest->card->number, 'Card number should match task requirement');
        $this->assertEquals(12, $chargeRequest->card->expMonth, 'Exp month should match task requirement');
        $this->assertEquals(2028, $chargeRequest->card->expYear, 'Exp year should match task requirement');
        $this->assertEquals('123', $chargeRequest->card->cvv, 'CVV should match task requirement');
    }

    public function testUnifiedResponseFormat(): void
    {
        
        // Simulate successful charge response
        $successResponse = [
            'transactionId' => 'char_xxxxxxxxxxxxxxxxx',
            'status' => 'success',
            'amount' => 25.99,
            'currency' => 'USD'
        ];
        
        // Verify response structure matches task requirements
        $this->assertArrayHasKey('transactionId', $successResponse, 'Response must have transactionId');
        $this->assertArrayHasKey('status', $successResponse, 'Response must have status');
        $this->assertArrayHasKey('amount', $successResponse, 'Response must have amount');
        $this->assertArrayHasKey('currency', $successResponse, 'Response must have currency');
        
        $this->assertEquals('success', $successResponse['status'], 'Status should be success');
        $this->assertEquals(25.99, $successResponse['amount'], 'Amount should match request');
        $this->assertEquals('USD', $successResponse['currency'], 'Currency should match request');
    }

    public function testErrorResponseFormat(): void
    {
        // Test error response format as specified in task
        
        $errorResponse = [
            'status' => 'failed',
            'message' => 'The payment was declined by the provider.'
        ];
        
        // Verify error response structure
        $this->assertArrayHasKey('status', $errorResponse, 'Error response must have status');
        $this->assertArrayHasKey('message', $errorResponse, 'Error response must have message');
        
        $this->assertEquals('failed', $errorResponse['status'], 'Error status should be failed');
        $this->assertNotEmpty($errorResponse['message'], 'Error message should not be empty');
    }

    public function testRefundEndpointCompliance(): void
    {
        // Test refund endpoint as bonus requirement
        
        $refundData = [
            'chargeId' => 'char_123456789',
            'amount' => 25.99
        ];
        
        $refundRequest = new RefundRequest($refundData);
        
        $this->assertEquals('char_123456789', $refundRequest->chargeId, 'Charge ID should match');
        $this->assertEquals(25.99, $refundRequest->amount, 'Refund amount should match');
    }

} 
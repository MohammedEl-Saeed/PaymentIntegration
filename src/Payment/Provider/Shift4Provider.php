<?php
namespace App\Payment\Provider;

use App\Payment\Contract\PaymentProviderInterface;
use App\Payment\Dto\ChargeRequest;
use App\Payment\Dto\ChargeResponse;
use App\Payment\Dto\RefundRequest;
use App\Payment\Dto\RefundResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Shift4Provider implements PaymentProviderInterface
{
    public function __construct(
        private HttpClientInterface $http,
        private string $secretKey,
        private string $baseUrl = 'https://api.shift4.com'
    ) {}

    public function getName(): string { return 'shift4'; }

    public function charge(ChargeRequest $req): ChargeResponse
    {
        // Shift4 expects amounts in cents (integer)
        $amountInCents = (int) round($req->amount * 100);

        $payload = [
            'amount'   => $amountInCents,
            'currency' => strtoupper($req->currency),
            'card'     => [
                'number'   => $req->card->number,
                'expMonth' => $req->card->expMonth,
                'expYear'  => $req->card->expYear,
                'cvc'      => $req->card->cvv,
            ],
        ];

        $response = $this->http->request('POST', $this->baseUrl.'/charges', [
            'auth_basic' => [$this->secretKey, ''],
            'headers'    => ['Content-Type' => 'application/json'],
            'json'       => $payload,
        ]);

        $status = $response->getStatusCode();
        $data   = $response->toArray(false);

        if ($status >= 200 && $status < 300 && isset($data['id'])) {
            return new ChargeResponse(
                transactionId: $data['id'],
                status: 'success',
                amount: $req->amount,
                currency: strtoupper($req->currency)
            );
        }

        // Standardize failure
        $message = $data['error']['message'] ?? 'The payment was declined by the provider.';
        return new ChargeResponse(
            transactionId: $data['id'] ?? '',
            status: 'failed',
            amount: $req->amount,
            currency: strtoupper($req->currency),
            message: $message
        );
    }

    public function refund(RefundRequest $req): RefundResponse
    {
        $payload = [];
        if ($req->amount !== null) {
            $payload['amount'] = (int) round($req->amount * 100);
        }
        $payload['chargeId'] = $req->transactionId ?? 0;

        $response = $this->http->request('POST', $this->baseUrl.'/refunds', [
            'auth_basic' => [$this->secretKey, ''],
            'headers'    => ['Content-Type' => 'application/json'],
            'json'       => $payload ?: (object)[],
        ]);

        $status = $response->getStatusCode();
        $data   = $response->toArray(false);

        if ($status >= 200 && $status < 300 && isset($data['id'])) {
            return new RefundResponse(
                refundId: $data['id'],
                status: 'success'
            );
        }

        $message = $data['error']['message'] ?? 'Refund failed at the provider.';
        return new RefundResponse(
            refundId: $data['id'] ?? '',
            status: 'failed',
            message: $message
        );
    }
}

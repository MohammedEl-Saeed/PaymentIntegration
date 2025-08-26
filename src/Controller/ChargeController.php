<?php
namespace App\Controller;

use App\Payment\Dto\RefundRequest;
use App\Payment\PaymentService;
use App\Payment\Dto\ChargeRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ChargeController extends AbstractController
{
    public function __construct(
        private PaymentService $paymentService,
        private ValidatorInterface $validator
    ) {}

    #[Route('/api/charge', name: 'api_charge', methods: ['POST'])]
    public function charge(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $dto = new ChargeRequest($data);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return new JsonResponse([
                'status' => 'failed',
                'message' => (string) $errors,
            ], 400);
        }

        try {
            $res = $this->paymentService->charge($dto);

            if ($res->status === 'success') {
                return new JsonResponse([
                    'transactionId' => $res->transactionId,
                    'status' => 'success',
                    'amount' => $res->amount,
                    'currency' => $res->currency,
                ], 200);
            }

            return new JsonResponse([
                'status' => 'failed',
                'message' => $res->message ?? 'The payment was declined by the provider.',
            ], 400);

        } catch (\Throwable $e) {
            return new JsonResponse([
                'status' => 'failed',
                'message' => 'Unexpected error while processing payment.',
            ], 500);
        }
    }

    #[Route('/api/refund', name: 'api_refund', methods: ['POST'])]
    public function refund(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $dto = new RefundRequest($data);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json([
                'status' => 'failed',
                'message' => (string) $errors,
            ], 400);
        }

        try {
            $res = $this->paymentService->refund($dto);
            if ($res->status === 'success') {
                return $this->json(['status' => 'success', 'refundId' => $res->refundId], 200);
            }

            return $this->json(['status' => 'failed', 'message' => $res->message], 400);
        } catch (\Throwable $e) {
            return $this->json([
                'status' => 'failed',
                'message' => 'Unexpected error while processing refund.',
            ], 500);
        }
    }
}

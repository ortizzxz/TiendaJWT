<?php

namespace Services;

use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class PayPalServices
{
    private $apiContext;

    public function __construct()
    {
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                $_ENV['PAYPAL_CLIENT_ID'],
                $_ENV['PAYPAL_CLIENT_SECRET'],
            )
        );

        $this->apiContext->setConfig([
            'mode' => $_ENV['PAYPAL_MODE']
        ]);
    }

    public function createPayment($total, $currency, $returnUrl, $cancelUrl)
    {
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        $amount = new Amount();
        $amount->setTotal($total);
        $amount->setCurrency($currency);

        $transaction = new Transaction();
        $transaction->setAmount($amount);
        $transaction->setDescription("Pago en tu tienda online");

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($returnUrl)->setCancelUrl($cancelUrl);

        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setTransactions([$transaction])
            ->setRedirectUrls($redirectUrls);

        try {
            $payment->create($this->apiContext);
            return $payment->getApprovalLink();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function executePayment($paymentId, $payerId)
    {
        $payment = Payment::get($paymentId, $this->apiContext);
        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        try {
            $result = $payment->execute($execution, $this->apiContext);
            return $result;
        } catch (\Exception $e) {
            return null;
        }
    }
}

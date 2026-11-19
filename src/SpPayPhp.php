<?php

namespace Sppay\SpPayPhp;

class SpPayPhp
{
    private string $baseUrl;

    public function __construct(
        Environment $environment = Environment::Production,
        ?string $baseUrl = null,
        private int $timeout = 60,
    ) {
        $this->baseUrl = $baseUrl ?? $environment->value;
    }

    public function generateAPIToken(
        $clientId,
        $clientSecret,
        $username,
        $password,
    ): array {
        return $this->post('/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'username' => $username,
            'password' => $password,
        ]);
    }

    public function initiatePayment(
        $bearerToken,
        $amount,
        $payerEmail,
        $payerAccountCode,
        $payerAccountNumber,
        $receiveAccountNumber = null,
        $callbackUrl = null,
        $reference = null,
        $payerAccountCvv = null,
        $payerAccountExpiry = null,
    ): array {
        return $this->post('/v1/api/payments/initiate', self::withoutNulls([
            'receive_account_no' => $receiveAccountNumber,
            'amount' => $amount,
            'payer' => [
                'email' => $payerEmail,
                'account' => self::withoutNulls([
                    'code' => $payerAccountCode,
                    'number' => $payerAccountNumber,
                    'cvv' => $payerAccountCvv,
                    'expiry' => $payerAccountExpiry,
                ]),
            ],
            'reference' => $reference,
            'callback_url' => $callbackUrl,
        ]), $bearerToken);
    }

    public function submitOTP(
        $bearerToken,
        $otp,
        $transactionReference,
    ): array {
        return $this->post('/v1/api/payments/otp/submit', [
            'otp' => $otp,
            'transaction_reference' => $transactionReference,
        ], $bearerToken);
    }

    public function validateAccount(
        $bearerToken,
        $institutionCode,
        $accountNumber,
    ): array {
        return $this->post('/v1/api/transfers/validate-account', [
            'institution_code' => $institutionCode,
            'account_number' => $accountNumber,
        ], $bearerToken);
    }

    public function validateTransfer(
        $bearerToken,
        $amount,
        $recipientAccountCode,
        $recipientAccountNumber,
        $sendAccountNumber = null,
        $customerReference = null,
        $recipientFirstName = null,
        $recipientLastName = null,
        $recipientTownOrCity = null,
        $recipientAddress = null,
        $recipientReasonForSending = null,
        $recipientCountryCode = null,
        $recipientStateOrRegionCode = null,
        $recipientIdType = null,
        $recipientIdReference = null,
    ): array {
        return $this->post('/v1/api/transfers/validate', self::transferBody(get_defined_vars()), $bearerToken);
    }

    public function submitTransfer(
        $bearerToken,
        $amount,
        $recipientAccountCode,
        $recipientAccountNumber,
        $sendAccountNumber = null,
        $customerReference = null,
        $recipientFirstName = null,
        $recipientLastName = null,
        $recipientTownOrCity = null,
        $recipientAddress = null,
        $recipientReasonForSending = null,
        $recipientCountryCode = null,
        $recipientStateOrRegionCode = null,
        $recipientIdType = null,
        $recipientIdReference = null,
        $callbackUrl = null,
    ): array {
        return $this->post('/v1/api/transfers/submit', self::transferBody(get_defined_vars()), $bearerToken);
    }

    public function sendSMS(
        $bearerToken,
        $approvedSenderID,
        $recipients,
        $message,
    ): array {
        return $this->post('/v1/api/sms/send', [
            'from' => $approvedSenderID,
            'to' => $recipients,
            'message' => $message,
        ], $bearerToken);
    }

    private function post(string $endpoint, array $body, ?string $bearerToken = null): array
    {
        return (new SpPayApiRequest(
            baseUrl: $this->baseUrl,
            endpoint: $endpoint,
            body: $body,
            bearerToken: $bearerToken,
            timeout: $this->timeout,
        ))->sendRequest();
    }

    private static function transferBody(array $args): array
    {
        return self::withoutNulls([
            'send_account_no' => $args['sendAccountNumber'],
            'amount' => $args['amount'],
            'recipient' => self::withoutNulls([
                'account' => [
                    'code' => $args['recipientAccountCode'],
                    'number' => $args['recipientAccountNumber'],
                ],
                'first_name' => $args['recipientFirstName'],
                'last_name' => $args['recipientLastName'],
                'town_or_city' => $args['recipientTownOrCity'],
                'address' => $args['recipientAddress'],
                'reason_for_sending' => $args['recipientReasonForSending'],
                'country_code' => $args['recipientCountryCode'],
                'state_or_region_code' => $args['recipientStateOrRegionCode'],
                'id_type' => $args['recipientIdType'],
                'id_reference' => $args['recipientIdReference'],
            ]),
            'reference' => $args['customerReference'],
            'callback_url' => $args['callbackUrl'] ?? null,
        ]);
    }

    private static function withoutNulls(array $values): array
    {
        return array_filter($values, fn ($value) => $value !== null);
    }
}

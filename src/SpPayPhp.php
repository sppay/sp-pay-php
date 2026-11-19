<?php

namespace Sppay\SpPayPhp;

use InvalidArgumentException;

class SpPayPhp
{
    private const HOSTS = [
        'production' => 'https://engine.sppaygh.com',
        'staging' => 'https://engine.sppay.dev',
    ];

    private string $base_url;

    public function __construct(
        string $environment = 'production',
        ?string $base_url = null,
        private int $timeout = 60,
    ) {
        if (! isset(self::HOSTS[$environment])) {
            throw new InvalidArgumentException("Unknown environment \"$environment\". Use \"production\" or \"staging\".");
        }

        $this->base_url = $base_url ?? self::HOSTS[$environment];
    }

    public function generateAPIToken(
        $client_id,
        $client_secret,
        $username,
        $password,
    ): array {
        return $this->post('/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'username' => $username,
            'password' => $password,
        ]);
    }

    public function initiatePayment(
        $bearer_token,
        $amount,
        $payer_email,
        $payer_account_code,
        $payer_account_number,
        $receive_account_number = null,
        $callback_url = null,
        $merchant_reference = null,
        $payer_account_cvv = null,
        $payer_account_expiry = null,
    ): array {
        return $this->post('/v1/api/payments/initiate', self::withoutNulls([
            'receive_account_no' => $receive_account_number,
            'amount' => $amount,
            'payer' => [
                'email' => $payer_email,
                'account' => self::withoutNulls([
                    'code' => $payer_account_code,
                    'number' => $payer_account_number,
                    'cvv' => $payer_account_cvv,
                    'expiry' => $payer_account_expiry,
                ]),
            ],
            'reference' => $merchant_reference,
            'callback_url' => $callback_url,
        ]), $bearer_token);
    }

    public function submitOTP(
        $bearer_token,
        $otp,
        $system_reference,
    ): array {
        return $this->post('/v1/api/payments/otp/submit', [
            'otp' => $otp,
            'transaction_reference' => $system_reference,
        ], $bearer_token);
    }

    public function validateAccount(
        $bearer_token,
        $institution_code,
        $account_number,
    ): array {
        return $this->post('/v1/api/transfers/validate-account', [
            'institution_code' => $institution_code,
            'account_number' => $account_number,
        ], $bearer_token);
    }

    public function validateTransfer(
        $bearer_token,
        $send_account_number,
        $amount,
        $recipient_account_code,
        $recipient_account_number,
        $merchant_reference = null,
        $recipient_first_name = null,
        $recipient_last_name = null,
        $recipient_town_or_city = null,
        $recipient_address = null,
        $recipient_reason_for_sending = null,
        $recipient_country_code = null,
        $recipient_state_or_region_code = null,
        $recipient_id_type = null,
        $recipient_id_reference = null,
    ): array {
        return $this->post('/v1/api/transfers/validate', self::transferBody(get_defined_vars()), $bearer_token);
    }

    public function submitTransfer(
        $bearer_token,
        $send_account_number,
        $amount,
        $recipient_account_code,
        $recipient_account_number,
        $merchant_reference = null,
        $recipient_first_name = null,
        $recipient_last_name = null,
        $recipient_town_or_city = null,
        $recipient_address = null,
        $recipient_reason_for_sending = null,
        $recipient_country_code = null,
        $recipient_state_or_region_code = null,
        $recipient_id_type = null,
        $recipient_id_reference = null,
        $callback_url = null,
    ): array {
        return $this->post('/v1/api/transfers/submit', self::transferBody(get_defined_vars()), $bearer_token);
    }

    public function sendSMS(
        $bearer_token,
        $approved_sender_id,
        $recipients,
        $message,
    ): array {
        return $this->post('/v1/api/sms/send', [
            'from' => $approved_sender_id,
            'to' => $recipients,
            'message' => $message,
        ], $bearer_token);
    }

    private function post(string $endpoint, array $body, ?string $bearer_token = null): array
    {
        return (new SpPayApiRequest(
            base_url: $this->base_url,
            endpoint: $endpoint,
            body: $body,
            bearer_token: $bearer_token,
            timeout: $this->timeout,
        ))->sendRequest();
    }

    private static function transferBody(array $args): array
    {
        return self::withoutNulls([
            'send_account_no' => $args['send_account_number'],
            'amount' => $args['amount'],
            'recipient' => self::withoutNulls([
                'account' => [
                    'code' => $args['recipient_account_code'],
                    'number' => $args['recipient_account_number'],
                ],
                'first_name' => $args['recipient_first_name'],
                'last_name' => $args['recipient_last_name'],
                'town_or_city' => $args['recipient_town_or_city'],
                'address' => $args['recipient_address'],
                'reason_for_sending' => $args['recipient_reason_for_sending'],
                'country_code' => $args['recipient_country_code'],
                'state_or_region_code' => $args['recipient_state_or_region_code'],
                'id_type' => $args['recipient_id_type'],
                'id_reference' => $args['recipient_id_reference'],
            ]),
            'reference' => $args['merchant_reference'],
            'callback_url' => $args['callback_url'] ?? null,
        ]);
    }

    private static function withoutNulls(array $values): array
    {
        return array_filter($values, fn ($value) => $value !== null);
    }
}

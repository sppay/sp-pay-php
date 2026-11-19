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
        $password
    ) {
        return (
            (
                new SpPayApiRequest(
                    base_url: $this->base_url,
                    method: 'POST',
                    endpoint: '/oauth/token',
                    body: [
                        'grant_type' => 'password',
                        'client_id' => $client_id,
                        'client_secret' => $client_secret,
                        'username' => $username,
                        'password' => $password,
                    ],
                )
            )->sendRequest()
        );
    }

    public function initiatePayment(
        $bearer_token,
        $recieve_account_number,
        $amount,
        $payer_email,
        $payer_account_code,
        $payer_account_number,
        $callback_url
    ) {
        return (
            (
                new SpPayApiRequest(
                    base_url: $this->base_url,
                    bearer_token: $bearer_token,
                    method: 'POST',
                    endpoint: '/v1/api/payments/initiate',
                    body: [
                        'receive_account_no' => $recieve_account_number,
                        'amount' => $amount,
                        'payer' => [
                            'email' => $payer_email,
                            'account' => [
                                'code' => $payer_account_code,
                                'number' => $payer_account_number,
                            ],
                        ],
                        'callback_url' => $callback_url,
                    ],
                )
            )->sendRequest()
        );
    }

    public function submitOTP(
        $bearer_token,
        $otp,
        $transaction_reference
    ) {
        return (
            (
                new SpPayApiRequest(
                    base_url: $this->base_url,
                    bearer_token: $bearer_token,
                    method: 'POST',
                    endpoint: '/v1/api/payments/otp/submit',
                    body: [
                        'otp' => $otp,
                        'transaction_reference' => $transaction_reference,
                    ],
                )
            )->sendRequest()
        );
    }

    public function validateAccount(
        $bearer_token,
        $institution_code,
        $account_number
    ) {
        return (
            (
                new SpPayApiRequest(
                    base_url: $this->base_url,
                    bearer_token: $bearer_token,
                    method: 'POST',
                    endpoint: '/v1/api/transfers/validate-account',
                    body: [
                        'institution_code' => $institution_code,
                        'account_number' => $account_number,
                    ],
                )
            )->sendRequest()
        );
    }

    public function validateTransfer(
        $callback_url,
        $bearer_token,
        $customer_reference,
        $send_account_number,
        $amount,
        $recipient_account_code,
        $recipient_account_number,
        $sender_reason_for_sending = null,
        $recipient_first_name = null,
        $recipient_last_name = null,
        $recipient_town_or_city = null,
        $recipient_address = null,
        $recipient_country_code = null,
        $recipient_state_or_region_code = null,
        $recipient_id_type = null,
        $recipient_id_reference = null,
    ) {
        return (
            (
                new SpPayApiRequest(
                    base_url: $this->base_url,
                    bearer_token: $bearer_token,
                    method: 'POST',
                    endpoint: '/v1/api/transfers/validate',
                    body: [
                        'send_account_no' => $send_account_number,
                        'amount' => $amount,
                        'recipient' => [
                            'account' => [
                                'code' => $recipient_account_code,
                                'number' => $recipient_account_number,
                            ],
                            'first_name' => $recipient_first_name,
                            'last_name' => $recipient_last_name,
                            'town_or_city' => $recipient_town_or_city,
                            'address' => $recipient_address,
                            'reason_for_sending' => $sender_reason_for_sending,
                            'country_code' => $recipient_country_code,
                            'state_or_region_code' => $recipient_state_or_region_code,
                            'id_type' => $recipient_id_type,
                            'id_reference' => $recipient_id_reference,
                        ],
                        'reference' => $customer_reference,
                        'callback_url' => $callback_url,
                    ],
                )
            )->sendRequest()
        );
    }

    public function submitTransfer(
        $callback_url,
        $bearer_token,
        $customer_reference,
        $send_account_number,
        $amount,
        $recipient_account_code,
        $recipient_account_number,
        $sender_reason_for_sending = null,
        $recipient_first_name = null,
        $recipient_last_name = null,
        $recipient_town_or_city = null,
        $recipient_address = null,
        $recipient_country_code = null,
        $recipient_state_or_region_code = null,
        $recipient_id_type = null,
        $recipient_id_reference = null,
    ) {
        return (
            (
                new SpPayApiRequest(
                    base_url: $this->base_url,
                    bearer_token: $bearer_token,
                    method: 'POST',
                    endpoint: '/v1/api/transfers/submit',
                    body: [
                        'send_account_no' => $send_account_number,
                        'amount' => $amount,
                        'recipient' => [
                            'account' => [
                                'code' => $recipient_account_code,
                                'number' => $recipient_account_number,
                            ],
                            'first_name' => $recipient_first_name,
                            'last_name' => $recipient_last_name,
                            'town_or_city' => $recipient_town_or_city,
                            'address' => $recipient_address,
                            'reason_for_sending' => $sender_reason_for_sending,
                            'country_code' => $recipient_country_code,
                            'state_or_region_code' => $recipient_state_or_region_code,
                            'id_type' => $recipient_id_type,
                            'id_reference' => $recipient_id_reference,
                        ],
                        'reference' => $customer_reference,
                        'callback_url' => $callback_url,
                    ],
                )
            )->sendRequest()
        );
    }

    public function sendSMS(
        $bearer_token,
        $approved_sender_id,
        $recipients,
        $message
    ) {
        return (
            (
                new SpPayApiRequest(
                    base_url: $this->base_url,
                    bearer_token: $bearer_token,
                    method: 'POST',
                    endpoint: '/v1/api/sms/send',
                    body: [
                        'from' => $approved_sender_id,
                        'to' => $recipients,
                        'message' => $message,
                    ],
                )
            )->sendRequest()
        );
    }
}

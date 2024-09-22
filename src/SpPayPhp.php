<?php

namespace Sppay\SpPayPhp;

class SpPayPhp
{
    // TODO: To be updated when systems go live.
    private $baseUrl = 'https://engine.sppay.dev';

    public function generateAPIToken(
        $clientId,
        $clientSecret,
        $username,
        $password
    ) {
        return (
            new SpPayApiRequest(
                baseUrl: $this->baseUrl,
                method: 'POST',
                endppoint: '/oauth/token',
                body: [
                    'grant_type' => 'password',
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'username' => $username,
                    'password' => $password,
                ],
            )
        );
    }

    public function initiatePayment(
        $bearerToken,
        $recieveAccountNumber,
        $amount,
        $payeeEmail,
        $payeeAccountCode,
        $payeeAccountNumber,
        $callbackUrl
    ) {
        return (
            new SpPayApiRequest(
                baseUrl: $this->baseUrl,
                bearerToken: $bearerToken,
                method: 'POST',
                endppoint: '/v1/api/payments/initiate',
                body: [
                    'receive_account_no' => $recieveAccountNumber,
                    'amount' => $amount,
                    'payee' => [
                        'email' => $payeeEmail,
                        'account' => [
                            'code' => $payeeAccountCode,
                            'number' => $payeeAccountNumber
                        ],
                    ],
                    'callback_url' => $callbackUrl
                ],
            )
        );
    }

    public function submitOTP(
        $bearerToken,
        $otp,
        $transactionReference
    ) {
        return (
            new SpPayApiRequest(
                baseUrl: $this->baseUrl,
                bearerToken: $bearerToken,
                method: 'POST',
                endppoint: '/v1/api/payments/otp/submit',
                body: [
                    'otp' => $otp,
                    'transaction_reference' => $transactionReference,
                ],
            )
        );
    }

    public function validateAccount(
        $bearerToken,
        $institutionCode,
        $accountNumber
    ) {
        return (
            new SpPayApiRequest(
                baseUrl: $this->baseUrl,
                bearerToken: $bearerToken,
                method: 'POST',
                endppoint: '/v1/api/transfers/validate-account',
                body: [
                    'institution_code' => $institutionCode,
                    'account_number' => $accountNumber,
                ],
            )
        );
    }

    public function validateTransfer(
        $bearerToken,
        $customerReference,
        $sendAccountNumber,
        $amount,
        $senderReasonForSending = null,
        $recipientAccountCode,
        $recipientAccountNumber,
        $recipientFirstName = null,
        $recipientLastName = null,
        $recipientTownOrCity = null,
        $recipientAddress = null,
        $recipientCountryCode = null,
        $recipientStateOrRegionCode = null,
        $recipientIdType = null,
        $recipientIdReference = null,
        $callbackUrl
    ) {
        return (
            new SpPayApiRequest(
                baseUrl: $this->baseUrl,
                bearerToken: $bearerToken,
                method: 'POST',
                endppoint: '/v1/api/transfers/validate',
                body: [
                    'send_account_no' => $sendAccountNumber,
                    'amount' => $amount,
                    'recipient' => [
                        'account' => [
                            'code' => $recipientAccountCode,
                            'number' => $recipientAccountNumber
                        ],
                        'first_name' => $recipientFirstName,
                        'last_name' => $recipientLastName,
                        'town_or_city' => $recipientTownOrCity,
                        'address' => $recipientAddress,
                        'reason_for_sending' => $senderReasonForSending,
                        'country_code' => $recipientCountryCode,
                        'state_or_region_code' => $recipientStateOrRegionCode,
                        'id_type' => $recipientIdType,
                        'id_reference' => $recipientIdReference,
                    ],
                    'reference' => $customerReference,
                    'callback_url' => $callbackUrl
                ],
            )
        );
    }

    public function submitTransfer(
        $bearerToken,
        $customerReference,
        $sendAccountNumber,
        $amount,
        $senderReasonForSending = null,
        $recipientAccountCode,
        $recipientAccountNumber,
        $recipientFirstName = null,
        $recipientLastName = null,
        $recipientTownOrCity = null,
        $recipientAddress = null,
        $recipientCountryCode = null,
        $recipientStateOrRegionCode = null,
        $recipientIdType = null,
        $recipientIdReference = null,
        $callbackUrl
    ) {
        return (
            new SpPayApiRequest(
                baseUrl: $this->baseUrl,
                bearerToken: $bearerToken,
                method: 'POST',
                endppoint: '/v1/api/transfers/submit',
                body: [
                    'send_account_no' => $sendAccountNumber,
                    'amount' => $amount,
                    'recipient' => [
                        'account' => [
                            'code' => $recipientAccountCode,
                            'number' => $recipientAccountNumber
                        ],
                        'first_name' => $recipientFirstName,
                        'last_name' => $recipientLastName,
                        'town_or_city' => $recipientTownOrCity,
                        'address' => $recipientAddress,
                        'reason_for_sending' => $senderReasonForSending,
                        'country_code' => $recipientCountryCode,
                        'state_or_region_code' => $recipientStateOrRegionCode,
                        'id_type' => $recipientIdType,
                        'id_reference' => $recipientIdReference,
                    ],
                    'reference' => $customerReference,
                    'callback_url' => $callbackUrl
                ],
            )
        );
    }

    public function sendSMS(
        $bearerToken,
        $approvedSenderID,
        $recipients,
        $message
    ) {
        return (
            new SpPayApiRequest(
                baseUrl: $this->baseUrl,
                bearerToken: $bearerToken,
                method: 'POST',
                endppoint: '/v1/api/sms/send',
                body: [
                    'from' => $approvedSenderID,
                    'to' => $recipients,
                    'message' => $message,
                ],
            )
        );
    }
}

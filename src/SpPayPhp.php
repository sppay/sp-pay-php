<?php

namespace Sppay\SpPayPhp;

class SpPayPhp
{
    public function generateAPIToken(
        $clientId,
        $clientSecret,
        $username,
        $password
    ) {}

    public function initiatePayment(
        $bearerToken,
        $recieveAccountNumber,
        $amount,
        $payeeEmail,
        $payeeAccountCode,
        $payeeAccountNumber,
        $callbackUrl
    ) {}

    public function submitOTP(
        $bearerToken,
        $otp,
        $transactionReference
    ) {}

    public function validateAccount(
        $bearerToken,
        $institutionCode,
        $accountNumber
    ) {}

    public function validateTransfer(
        $bearerToken,
        $customerReference,
        $sendAccountNumber,
        $amount,
        $recipientAccountCode,
        $recipientAccountNumber,
        $recipientFirstName = null,
        $recipientLastName = null,
        $recipientTownOrCity = null,
        $recipientAddress = null,
        $recipientReasonForSending = null,
        $recipientCountryCode = null,
        $recipientStateOrRegionCode = null,
        $recipientIdType = null,
        $recipientIdReference = null,
        $callbackUrl
    ) {}

    public function submitTransfer(
        $bearerToken,
        $customerReference,
        $sendAccountNumber,
        $amount,
        $recipientAccountCode,
        $recipientAccountNumber,
        $recipientFirstName = null,
        $recipientLastName = null,
        $recipientTownOrCity = null,
        $recipientAddress = null,
        $recipientReasonForSending = null,
        $recipientCountryCode = null,
        $recipientStateOrRegionCode = null,
        $recipientIdType = null,
        $recipientIdReference = null,
        $callbackUrl
    ) {}

    public function sendSMS(
        $bearerToken,
        $approvedSenderID,
        $recipients,
        $message
    ) {}
}

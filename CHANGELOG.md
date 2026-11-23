# Changelog

## 2.0.0 - 2026-11-23

Breaking:

- Requires PHP 8.3 or later and ext-curl (#27).
- Defaults to production (engine.sppaygh.com). Pass `environment: 'staging'` for engine.sppay.dev, or `base_url` to override the host (#19).
- Arguments, variables and properties are snake_case, and the argument order changed in the payment and transfer methods.
- `customer_reference` is now `merchant_reference`, `transaction_reference` in `submitOTP` is now `system_reference`, and `send_account_number` is required (#21).
- Responses are returned as the API sent them, with the HTTP status in `http_status` (#24).
- Failures return the API's error shape with code `REQUEST_FAILED`, `INVALID_REQUEST` or `INVALID_RESPONSE` instead of a string (#20, #26).

Added:

- `initiatePayment` takes `merchant_reference`, `payer_account_cvv` and `payer_account_expiry`, and `receive_account_number` and `callback_url` are optional (#21).
- A request timeout, 60 seconds by default and settable with `timeout`, plus a 10 second connect timeout (#25).

Fixed:

- The README example (#22).
- CI no longer runs the staging tests, which made real transfers and sent SMS.

## 1.1.0 - 2024-09-22

- Added tests for endpoints for development.

## 1.0.1 - 2024-09-22

- Decode response from API in SP Pay API Request class.

## 1.0.0 - 2024-09-22

- Initial Release.

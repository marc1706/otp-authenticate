### About

This library implements OTP authentication into PHP. It allows users to generate TOTP and HOTP codes for two-factor authentication.
Generated codes can be created using sha1, sha256, or sha512 hashes. Whilst Google Authenticator will only support the first hash type, the latter
two are properly supported by apps like FreeOTP.

Codes are compared using a constant time comparison method and secrets are generated using openssl_random_pseudo_bytes().

### Requirements

PHP 5.3.0 or newer is required for this library to work.

### Automated Testing

The library is being tested using unit tests to prevent possible issues.

[![Build Status](https://travis-ci.org/marc1706/OTPAuthenticate.svg?branch=master)](https://travis-ci.org/marc1706/OTPAuthenticate)
[![Code Coverage](https://scrutinizer-ci.com/g/marc1706/OTPAuthenticate/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/marc1706/OTPAuthenticate/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/marc1706/OTPAuthenticate/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/marc1706/OTPAuthenticate/?branch=master)

### License

[The MIT License (MIT)](http://opensource.org/licenses/MIT)

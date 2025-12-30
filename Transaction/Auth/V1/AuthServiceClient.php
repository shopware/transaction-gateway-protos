<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Transaction\Auth\V1;

/**
 * AuthService provides OAuth 2.0 authentication and token management.
 * This service handles the OAuth flow itself, so it does NOT require authentication.
 */
class AuthServiceClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * ExchangeCode exchanges an OAuth authorization code for access and refresh tokens.
     * This is the first step after user completes OAuth consent flow.
     * @param \Transaction\Auth\V1\ExchangeCodeRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Transaction\Auth\V1\ExchangeCodeResponse>
     */
    public function ExchangeCode(\Transaction\Auth\V1\ExchangeCodeRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/transaction.auth.v1.AuthService/ExchangeCode',
        $argument,
        ['\Transaction\Auth\V1\ExchangeCodeResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * RefreshToken refreshes an expired access token using the provided refresh token.
     * Use this when the access token has expired but refresh token is still valid.
     * @param \Transaction\Auth\V1\RefreshTokenRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Transaction\Auth\V1\RefreshTokenResponse>
     */
    public function RefreshToken(\Transaction\Auth\V1\RefreshTokenRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/transaction.auth.v1.AuthService/RefreshToken',
        $argument,
        ['\Transaction\Auth\V1\RefreshTokenResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * GetUserAccount fetches OIDC userinfo for a given access token.
     * Returns user profile information from the identity provider.
     * @param \Transaction\Auth\V1\GetUserAccountRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Transaction\Auth\V1\GetUserAccountResponse>
     */
    public function GetUserAccount(\Transaction\Auth\V1\GetUserAccountRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/transaction.auth.v1.AuthService/GetUserAccount',
        $argument,
        ['\Transaction\Auth\V1\GetUserAccountResponse', 'decode'],
        $metadata, $options);
    }

}

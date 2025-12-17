<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Auth;

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
     * @param \Auth\ExchangeCodeRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Auth\ExchangeCodeResponse>
     */
    public function ExchangeCode(\Auth\ExchangeCodeRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/auth.AuthService/ExchangeCode',
        $argument,
        ['\Auth\ExchangeCodeResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * RefreshToken refreshes an expired access token using the provided refresh token.
     * Use this when the access token has expired but refresh token is still valid.
     * @param \Auth\RefreshTokenRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Auth\RefreshTokenResponse>
     */
    public function RefreshToken(\Auth\RefreshTokenRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/auth.AuthService/RefreshToken',
        $argument,
        ['\Auth\RefreshTokenResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * GetUserAccount fetches OIDC userinfo for a given access token.
     * Returns user profile information from the identity provider.
     * @param \Auth\GetUserAccountRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Auth\GetUserAccountResponse>
     */
    public function GetUserAccount(\Auth\GetUserAccountRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/auth.AuthService/GetUserAccount',
        $argument,
        ['\Auth\GetUserAccountResponse', 'decode'],
        $metadata, $options);
    }

}

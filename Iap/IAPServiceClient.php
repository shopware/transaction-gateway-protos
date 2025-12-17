<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Iap;

/**
 * IAPService provides in-app purchase operations for Shopware extensions.
 * All methods require OAuth authentication via metadata headers unless overridden at method level.
 */
class IAPServiceClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * CalculateBasket calculates pricing for a basket of in-app purchases.
     * Returns tax rates and itemized positions with pricing details.
     * @param \Iap\CalculateBasketRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Iap\CalculateBasketResponse>
     */
    public function CalculateBasket(\Iap\CalculateBasketRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/iap.IAPService/CalculateBasket',
        $argument,
        ['\Iap\CalculateBasketResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * PlaceOrder finalizes an in-app purchase order.
     * Requires a previously calculated basket with valid positions.
     * @param \Iap\PlaceOrderRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Iap\PlaceOrderResponse>
     */
    public function PlaceOrder(\Iap\PlaceOrderRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/iap.IAPService/PlaceOrder',
        $argument,
        ['\Iap\PlaceOrderResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * GetInAppFeature retrieves details about a specific in-app feature.
     * Returns feature configuration and pricing variants.
     * @param \Iap\GetInAppFeatureRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Iap\GetInAppFeatureResponse>
     */
    public function GetInAppFeature(\Iap\GetInAppFeatureRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/iap.IAPService/GetInAppFeature',
        $argument,
        ['\Iap\GetInAppFeatureResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * GetInAppPlans retrieves all in-app plans for a specific extension.
     * This is a public API that uses M2M authentication (no user OAuth token required).
     * @param \Iap\GetInAppPlansRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Iap\GetInAppPlansResponse>
     */
    public function GetInAppPlans(\Iap\GetInAppPlansRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/iap.IAPService/GetInAppPlans',
        $argument,
        ['\Iap\GetInAppPlansResponse', 'decode'],
        $metadata, $options);
    }

}

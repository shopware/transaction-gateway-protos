<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Transaction\Iap\V1;

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
     * @param \Transaction\Iap\V1\CalculateBasketRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Transaction\Iap\V1\CalculateBasketResponse>
     */
    public function CalculateBasket(\Transaction\Iap\V1\CalculateBasketRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/transaction.iap.v1.IAPService/CalculateBasket',
        $argument,
        ['\Transaction\Iap\V1\CalculateBasketResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * PlaceOrder finalizes an in-app purchase order.
     * Requires a previously calculated basket with valid positions.
     * @param \Transaction\Iap\V1\PlaceOrderRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Transaction\Iap\V1\PlaceOrderResponse>
     */
    public function PlaceOrder(\Transaction\Iap\V1\PlaceOrderRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/transaction.iap.v1.IAPService/PlaceOrder',
        $argument,
        ['\Transaction\Iap\V1\PlaceOrderResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * GetInAppFeature retrieves details about a specific in-app feature.
     * Returns feature configuration and pricing variants.
     * @param \Transaction\Iap\V1\GetInAppFeatureRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Transaction\Iap\V1\GetInAppFeatureResponse>
     */
    public function GetInAppFeature(\Transaction\Iap\V1\GetInAppFeatureRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/transaction.iap.v1.IAPService/GetInAppFeature',
        $argument,
        ['\Transaction\Iap\V1\GetInAppFeatureResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * GetInAppPlans retrieves all in-app plans for a specific extension.
     * This is a public API that uses M2M authentication (no user OAuth token required).
     * @param \Transaction\Iap\V1\GetInAppPlansRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Transaction\Iap\V1\GetInAppPlansResponse>
     */
    public function GetInAppPlans(\Transaction\Iap\V1\GetInAppPlansRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/transaction.iap.v1.IAPService/GetInAppPlans',
        $argument,
        ['\Transaction\Iap\V1\GetInAppPlansResponse', 'decode'],
        $metadata, $options);
    }

}

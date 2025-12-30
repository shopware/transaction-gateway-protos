<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Transaction\Purchases\V1;

/**
 * PurchasesService provides access to cached purchase/subscription data
 * synchronized from the SBP API and stored in Redis by domain.
 */
class PurchasesServiceClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * GetPurchasesByDomain retrieves all purchases for a specific shop domain.
     * The domain should be passed as a query parameter (e.g., "sw9.service.com").
     * @param \Transaction\Purchases\V1\GetPurchasesByDomainRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Transaction\Purchases\V1\GetPurchasesByDomainResponse>
     */
    public function GetPurchasesByDomain(\Transaction\Purchases\V1\GetPurchasesByDomainRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/transaction.purchases.v1.PurchasesService/GetPurchasesByDomain',
        $argument,
        ['\Transaction\Purchases\V1\GetPurchasesByDomainResponse', 'decode'],
        $metadata, $options);
    }

}

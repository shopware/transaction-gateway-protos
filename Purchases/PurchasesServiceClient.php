<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Purchases;

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
     * @param \Purchases\GetPurchasesByDomainRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Purchases\GetPurchasesByDomainResponse>
     */
    public function GetPurchasesByDomain(\Purchases\GetPurchasesByDomainRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/purchases.PurchasesService/GetPurchasesByDomain',
        $argument,
        ['\Purchases\GetPurchasesByDomainResponse', 'decode'],
        $metadata, $options);
    }

}

#!/usr/bin/env php
<?php
/**
 * Example usage of Transaction Gateway gRPC client in PHP.
 * 
 * Prerequisites:
 * 1. PHP 8.1+ with grpc extension
 * 2. composer install
 * 3. Ensure gRPC server is running on localhost:50051
 * 4. php example.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use Grpc\ChannelCredentials;
use Transaction\Purchases\V1\GetPurchasesByDomainRequest;
use Transaction\Purchases\V1\PurchasesServiceClient;

// Gateway endpoint for local development
const ENDPOINT = 'localhost:50051';

function getPurchasesByDomain(): void
{
    echo "GetPurchasesByDomain...\n\n";

    // Create insecure connection for local development
    $options = [
        'credentials' => ChannelCredentials::createInsecure(),
    ];

    // Create the client
    $client = new PurchasesServiceClient(ENDPOINT, $options);

    // Set required metadata headers
    $metadata = [
        'x-request-service' => ['example-php-test'],
        'x-request-id' => ['test-request-001'], // Optional
    ];

    // Create the request
    $request = new GetPurchasesByDomainRequest();
    $request->setDomain('example-shop.com');

    // Make the request
    [$response, $status] = $client->GetPurchasesByDomain($request, $metadata)->wait();

    if ($status->code !== \Grpc\STATUS_OK) {
        // Check if it's a NotFound error (expected for test domain)
        if ($status->code === \Grpc\STATUS_NOT_FOUND) {
            echo "✅ Connection successful - got expected NotFound for test domain\n";
            echo "❌ Error: {$status->details}\n\n";
            return;
        }
        
        echo "❌ GetPurchasesByDomain failed: ({$status->code}) {$status->details}\n";
        exit(1);
    }

    // Print results if purchases exist
    $purchases = $response->getPurchases();
    echo sprintf("✅ Found %d purchases:\n", count($purchases));
    
    foreach ($purchases as $purchase) {
        echo sprintf(
            "  Plugin: %s, Identifier: %s, Quantity: %d\n",
            $purchase->getPluginName(),
            $purchase->getIdentifier(),
            $purchase->getQuantity()
        );
    }

    echo "\n✅\n";
}

// Run
echo str_repeat('=', 50) . "\n";
echo "Transaction Gateway PHP Example\n";
echo str_repeat('=', 50) . "\n\n";

getPurchasesByDomain();

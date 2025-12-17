/**
 * Example usage of PurchasesService client with metadata headers.
 * This file validates the documentation example compiles correctly.
 * 
 * To test: npx tsc --noEmit pkg/proto/typescript/purchases/example.ts
 */

import { PurchasesServiceClient } from './purchases';
import { credentials, Metadata } from '@grpc/grpc-js';

// Example: Creating client and making request with required metadata
function exampleUsage(): void {
  const client = new PurchasesServiceClient(
    'transaction-gateway.example.com:443',
    credentials.createSsl()
  );

  // Required: Set metadata headers
  const metadata = new Metadata();
  metadata.set('x-request-service', 'my-service-name');
  metadata.set('x-request-id', 'optional-trace-id'); // Optional

  client.getPurchasesByDomain(
    { domain: 'my-shop.example.com' },
    metadata,
    (err, response) => {
      if (err) {
        console.error('Error:', err);
        return;
      }
      
      console.log('Purchases:', response.purchases);
      
      // Access individual purchase fields
      for (const purchase of response.purchases) {
        console.log(`Plugin: ${purchase.pluginName}`);
        console.log(`Identifier: ${purchase.identifier}`);
        console.log(`Next Booking: ${purchase.nextBookingDate}`);
        console.log(`Quantity: ${purchase.quantity}`);
        if (purchase.pendingDowngradeIdentifier) {
          console.log(`Pending Downgrade: ${purchase.pendingDowngradeIdentifier}`);
        }
      }
    }
  );
}

// Alternative: Using callback without options
function exampleWithoutOptions(): void {
  const client = new PurchasesServiceClient(
    'transaction-gateway.example.com:443',
    credentials.createSsl()
  );

  const metadata = new Metadata();
  metadata.set('x-request-service', 'another-service');

  // This overload also works (without CallOptions)
  client.getPurchasesByDomain(
    { domain: 'shop.example.com' },
    metadata,
    (err, response) => {
      if (err) {
        console.error('Error:', err);
        return;
      }
      console.log('Got', response.purchases.length, 'purchases');
    }
  );
}

export { exampleUsage, exampleWithoutOptions };


/**
 * Example usage of Transaction Gateway gRPC client in TypeScript.
 * 
 * Prerequisites:
 * 1. npm install
 * 2. Ensure gRPC server is running on localhost:50051
 * 3. npx tsx example.ts
 */
import { credentials, Metadata, status } from '@grpc/grpc-js';
import { fileURLToPath } from 'url';
import { dirname, join } from 'path';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

// Import the purchases module using absolute path to bypass exports
const purchasesPath = join(__dirname, 'node_modules/@shopware/transaction-gateway-client/purchases/v1/purchases.ts');
const { PurchasesServiceClient } = await import(purchasesPath);

// Gateway endpoint for local development
const ENDPOINT = 'localhost:50051';

async function getPurchasesByDomain(): Promise<void> {
    console.log('GetPurchasesByDomain...\n');

    return new Promise((resolve, reject) => {
        // Create insecure credentials for local development
        const creds = credentials.createInsecure();

        // Create the client
        const client = new PurchasesServiceClient(ENDPOINT, creds);

        // Set required metadata headers
        const metadata = new Metadata();
        metadata.set('x-request-service', 'example-ts-test');
        metadata.set('x-request-id', 'test-request-001'); // Optional

        // Make the request
        client.getPurchasesByDomain(
            { domain: 'example-shop.com' },
            metadata,
            (error: any, response: any) => {
                if (error) {
                    // Check for expected errors
                    if (error.code === status.NOT_FOUND) {
                        console.log('✅ Connection successful - got expected NotFound for test domain');
                        console.log(`❌ Error: ${error.message}\n`);
                        client.close();
                        resolve();
                        return;
                    }
                    if (error.code === status.UNIMPLEMENTED) {
                        console.log('✅ gRPC client works - server does not implement this service');
                        console.log(`   (This is expected if running against a different server)\n`);
                        client.close();
                        resolve();
                        return;
                    }
                    if (error.code === status.UNAVAILABLE) {
                        console.log('❌ Server unavailable - is the gRPC server running on ' + ENDPOINT + '?');
                        client.close();
                        resolve();
                        return;
                    }
                    console.error('❌ GetPurchasesByDomain failed:', error.message);
                    client.close();
                    reject(error);
                    return;
                }

                // Print results if purchases exist
                console.log(`✅ Found ${response.purchases.length} purchases:`);
                for (const purchase of response.purchases) {
                    console.log(`  Plugin: ${purchase.pluginName}, Identifier: ${purchase.identifier}, Quantity: ${purchase.quantity}`);
                }
                client.close();
                resolve();
            }
        );
    });
}

// Run 
async function main() {
    console.log('='.repeat(50));
    console.log('Transaction Gateway TypeScript Example\n');
    console.log('='.repeat(50) + '\n');

    await getPurchasesByDomain();
}

main().catch((err) => {
    console.error('❌ Failed to run example:', err);
});

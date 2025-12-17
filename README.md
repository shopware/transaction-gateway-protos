# Transaction Gateway Client Usage Guide

This guide provides detailed instructions for installing and using the Transaction Gateway gRPC clients in your applications.

## Table of Contents

- [Overview](#overview)
- [PHP Client](#php-client)
- [TypeScript Client](#typescript-client)
- [Go Client](#go-client)
- [Available Services](#available-services)
- [Troubleshooting](#troubleshooting)

---

## Overview

The Transaction Gateway exposes gRPC services for authentication, in-app purchases, and purchase data retrieval. Generated client libraries are distributed via Git branches:

| Language   | Branch              | Package Manager |
|------------|---------------------|-----------------|
| PHP        | `release-php-proto` | Composer        |
| TypeScript | `release-ts-proto`  | npm/yarn        |
| Go         | `release-go-proto`  | Go Modules      |

All clients are published to: `github.com/shopware/transaction-gateway-protos`

---

## PHP Client

### Prerequisites

- PHP 8.1+
- gRPC PHP extension (`ext-grpc`)
- Composer

**Install gRPC extension:**

```bash
# Using PECL
pecl install grpc

# Add to php.ini
echo "extension=grpc.so" >> $(php -i | grep "Loaded Configuration File" | cut -d' ' -f5)
```

### Installation

**Step 1: Add Repository to `composer.json`**

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/shopware/transaction-gateway-protos"
        }
    ]
}
```

**Step 2: Require the Package**

```bash
# For development (latest from branch)
composer require shopware/transaction-gateway-client:dev-release-php-proto

# For production (specific version)
composer require shopware/transaction-gateway-client:^1.0
```

Or add directly to `composer.json`:

```json
{
    "require": {
        "shopware/transaction-gateway-client": "dev-release-php-proto"
    }
}
```

### Usage Examples

#### Creating a Client Connection

```php
<?php

require_once 'vendor/autoload.php';

use Grpc\ChannelCredentials;

// Gateway endpoint
$endpoint = 'transaction-gateway.example.com:443';

// Connection options
$options = [
    'credentials' => ChannelCredentials::createSsl(),
    // Optional: Set timeout (in seconds)
    'timeout' => 30000000, // 30 seconds in microseconds
];

// For local development (insecure)
// $options = ['credentials' => ChannelCredentials::createInsecure()];
```

#### Using PurchasesService

```php
<?php

use Purchases\PurchasesServiceClient;
use Purchases\GetPurchasesByDomainRequest;

$client = new PurchasesServiceClient($endpoint, $options);

$metadata = [
    'x-request-service' => ['my-php-service'],
    'x-request-id' => [uniqid('req-')],
];

$request = new GetPurchasesByDomainRequest();
$request->setDomain('my-shop.example.com');

[$response, $status] = $client->GetPurchasesByDomain($request, $metadata)->wait();

if ($status->code === \Grpc\STATUS_OK) {
    foreach ($response->getPurchases() as $purchase) {
        echo sprintf(
            "Plugin: %s | Identifier: %s | Quantity: %d | Next Billing: %s\n",
            $purchase->getPluginName(),
            $purchase->getIdentifier(),
            $purchase->getQuantity(),
            $purchase->getNextBookingDate()
        );
        
        // Check for pending downgrades
        if ($purchase->hasPendingDowngradeIdentifier()) {
            echo "  → Pending downgrade to: " . $purchase->getPendingDowngradeIdentifier() . PHP_EOL;
        }
    }
} else {
    echo "Error ({$status->code}): {$status->details}" . PHP_EOL;
}
```

---

## TypeScript Client

### Prerequisites

- Node.js 18+
- npm or yarn

### Installation

```bash
# Install from branch (latest dev)
npm install git+https://github.com/shopware/transaction-gateway-protos.git#release-ts-proto

# Install specific version
npm install git+https://github.com/shopware/transaction-gateway-protos.git#v1.0.0

# Using yarn
yarn add git+https://github.com/shopware/transaction-gateway-protos.git#release-ts-proto
```

**Install peer dependencies:**

```bash
npm install nice-grpc long
```

Or add to `package.json`:

```json
{
    "dependencies": {
        "@shopware/transaction-gateway-client": "git+https://github.com/shopware/transaction-gateway-protos.git#release-ts-proto",
        "nice-grpc": "^2.0.0",
        "long": "^5.0.0"
    }
}
```

### Usage Examples

#### Creating a Client Connection

```typescript
import { createChannel, createClient, Metadata } from 'nice-grpc';

// For TLS connection (production)
const channel = createChannel('transaction-gateway.example.com:443');

// For insecure connection (local development)
// import { createChannel } from 'nice-grpc';
// const channel = createChannel('localhost:50051', ChannelCredentials.createInsecure());

// Helper to create metadata
function createRequestMetadata(serviceName: string, requestId?: string): Metadata {
    const metadata = new Metadata();
    metadata.set('x-request-service', serviceName);
    if (requestId) {
        metadata.set('x-request-id', requestId);
    }
    return metadata;
}
```

#### Using PurchasesService

```typescript
import { PurchasesServiceDefinition } from '@shopware/transaction-gateway-client/purchases/purchases';
import { createChannel, createClient, Metadata } from 'nice-grpc';

const channel = createChannel('transaction-gateway.example.com:443');
const purchasesClient = createClient(PurchasesServiceDefinition, channel);

async function getPurchases(domain: string) {
    const metadata = new Metadata();
    metadata.set('x-request-service', 'my-ts-service');
    metadata.set('x-request-id', `req-${Date.now()}`);

    try {
        const response = await purchasesClient.getPurchasesByDomain(
            { domain },
            { metadata }
        );

        for (const purchase of response.purchases) {
            console.log(`Plugin: ${purchase.pluginName}`);
            console.log(`  Identifier: ${purchase.identifier}`);
            console.log(`  Quantity: ${purchase.quantity}`);
            console.log(`  Next Billing: ${purchase.nextBookingDate}`);
            
            if (purchase.pendingDowngradeIdentifier) {
                console.log(`  Pending Downgrade: ${purchase.pendingDowngradeIdentifier}`);
            }
        }

        return response.purchases;
    } catch (error) {
        console.error('Failed to get purchases:', error);
        throw error;
    }
}

// Usage
getPurchases('my-shop.example.com');
```

---

## Go Client

### Prerequisites

- Go 1.21+

### Installation

**Step 1: Configure Go for Private Repositories**

```bash
# Set GOPRIVATE environment variable
export GOPRIVATE=github.com/shopware/transaction-gateway-protos

# Add to your shell profile (~/.bashrc, ~/.zshrc, etc.)
echo 'export GOPRIVATE=github.com/shopware/transaction-gateway-protos' >> ~/.zshrc

# Configure Git to use SSH (if using SSH keys)
git config --global url."git@github.com:".insteadOf "https://github.com/"
```

**Step 2: Install the Package**

```bash
# Get the latest version
go get github.com/shopware/transaction-gateway-protos@latest

# Get a specific version
go get github.com/shopware/transaction-gateway-protos@v1.0.0

# Get from branch (for development)
go get github.com/shopware/transaction-gateway-protos@release-go-proto
```

### Usage Examples

#### Creating a Client Connection

```go
package main

import (
    "context"
    "log"
    "time"

    "google.golang.org/grpc"
    "google.golang.org/grpc/credentials"
    "google.golang.org/grpc/credentials/insecure"
    "google.golang.org/grpc/metadata"
)

func main() {
    // For TLS connection (production)
    conn, err := grpc.Dial(
        "transaction-gateway.example.com:443",
        grpc.WithTransportCredentials(credentials.NewTLS(nil)),
    )
    if err != nil {
        log.Fatalf("Failed to connect: %v", err)
    }
    defer conn.Close()

    // For insecure connection (local development)
    // conn, err := grpc.Dial(
    //     "localhost:50051",
    //     grpc.WithTransportCredentials(insecure.NewCredentials()),
    // )
}

// Helper to create context with metadata
func createContext(serviceName, requestID string) context.Context {
    ctx := context.Background()
    
    // Add timeout
    ctx, _ = context.WithTimeout(ctx, 30*time.Second)
    
    // Add required metadata headers
    md := metadata.Pairs(
        "x-request-service", serviceName,
        "x-request-id", requestID,
    )
    
    return metadata.NewOutgoingContext(ctx, md)
}
```

#### Using PurchasesService

```go
package main

import (
    "context"
    "fmt"
    "log"

    purchasespb "github.com/shopware/transaction-gateway-protos/purchases"
    "google.golang.org/grpc"
    "google.golang.org/grpc/credentials"
    "google.golang.org/grpc/metadata"
)

func main() {
    conn, err := grpc.Dial(
        "transaction-gateway.example.com:443",
        grpc.WithTransportCredentials(credentials.NewTLS(nil)),
    )
    if err != nil {
        log.Fatalf("Failed to connect: %v", err)
    }
    defer conn.Close()

    client := purchasespb.NewPurchasesServiceClient(conn)

    // Create context with required metadata
    ctx := metadata.AppendToOutgoingContext(
        context.Background(),
        "x-request-service", "my-go-service",
        "x-request-id", "req-67890",
    )

    // Get purchases for a domain
    resp, err := client.GetPurchasesByDomain(ctx, &purchasespb.GetPurchasesByDomainRequest{
        Domain: "my-shop.example.com",
    })
    if err != nil {
        log.Fatalf("GetPurchasesByDomain failed: %v", err)
    }

    fmt.Printf("Found %d purchases:\n", len(resp.Purchases))
    for _, purchase := range resp.Purchases {
        fmt.Printf("  Plugin: %s\n", purchase.PluginName)
        fmt.Printf("    Identifier: %s\n", purchase.Identifier)
        fmt.Printf("    Quantity: %d\n", purchase.Quantity)
        fmt.Printf("    Next Billing: %s\n", purchase.NextBookingDate)
        
        if purchase.PendingDowngradeIdentifier != nil {
            fmt.Printf("    Pending Downgrade: %s\n", *purchase.PendingDowngradeIdentifier)
        }
    }
}
```

---

## Available Services

### IAPService

| RPC                    | Description                                          |
|------------------------|------------------------------------------------------|
| `GetInAppPlans`        | Retrieve in-app plans for a specific extension       |

### PurchasesService

| RPC                    | Description                                          |
|------------------------|------------------------------------------------------|
| `GetPurchasesByDomain` | Retrieves all purchases for a specific shop domain   |

---

## Required Metadata Headers

All requests **must** include these metadata headers:

| Header              | Required | Description                                           |
|---------------------|----------|-------------------------------------------------------|
| `x-request-service` | Yes      | Service name for log correlation (e.g., "my-service") |
| `x-request-id`      | No       | Request tracing ID (auto-generated if not provided)   |

---

## Troubleshooting

### Authentication Issues

**Problem:** `Permission denied` or `Repository not found`

**Solution:**
1. Verify your GitHub credentials are configured
2. Check that your PAT or SSH key has access to the repository
3. For Go, ensure `GOPRIVATE` is set correctly

### Connection Issues

**Problem:** `Connection refused` or `Timeout`

**Solution:**
1. Verify the gateway endpoint and port
2. Check firewall rules
3. For TLS connections, ensure certificates are valid
4. Try insecure connection for local development

### Version Mismatch

**Problem:** Incompatible proto definitions

**Solution:**
1. Update to the latest client version
2. Check release notes for breaking changes
3. Regenerate clients if using custom build

### PHP gRPC Extension

**Problem:** `Class 'Grpc\ChannelCredentials' not found`

**Solution:**
```bash
# Install gRPC extension
pecl install grpc

# Verify installation
php -m | grep grpc

# Add to php.ini if not present
echo "extension=grpc.so" | sudo tee -a /etc/php/8.1/cli/conf.d/20-grpc.ini
```

### Go Module Issues

**Problem:** `go get` fails with authentication error

**Solution:**
```bash
# Ensure GOPRIVATE is set
export GOPRIVATE=github.com/shopware/transaction-gateway-protos

# Clear module cache if needed
go clean -modcache

# Try with explicit version
go get github.com/shopware/transaction-gateway-protos@v1.0.0
```

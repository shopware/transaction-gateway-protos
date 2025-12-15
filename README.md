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

#### Using AuthService

```php
<?php

use Auth\AuthServiceClient;
use Auth\ExchangeCodeRequest;
use Auth\RefreshTokenRequest;
use Auth\GetUserAccountRequest;
use Auth\LogoutRequest;

$client = new AuthServiceClient($endpoint, $options);

// Required metadata headers
$metadata = [
    'x-request-service' => ['my-php-service'],
    'x-request-id' => [uniqid('req-')], // Optional but recommended
];

// Exchange OAuth authorization code for tokens
$request = new ExchangeCodeRequest();
$request->setCode('authorization_code_from_oauth_redirect');
$request->setSessionId('unique-session-id');

[$response, $status] = $client->ExchangeCode($request, $metadata)->wait();

if ($status->code === \Grpc\STATUS_OK) {
    echo "Access Token: " . $response->getAccessToken() . PHP_EOL;
    echo "Session ID: " . $response->getSessionId() . PHP_EOL;
} else {
    echo "Error: " . $status->details . PHP_EOL;
}

// Refresh an expired token
$refreshRequest = new RefreshTokenRequest();
$refreshRequest->setSessionId('existing-session-id');

[$refreshResponse, $status] = $client->RefreshToken($refreshRequest, $metadata)->wait();

// Get user account info
$userRequest = new GetUserAccountRequest();
$userRequest->setAccessToken('valid-access-token');

[$userResponse, $status] = $client->GetUserAccount($userRequest, $metadata)->wait();

if ($status->code === \Grpc\STATUS_OK) {
    echo "User ID: " . $userResponse->getUserId() . PHP_EOL;
    echo "Email: " . $userResponse->getEmail() . PHP_EOL;
}

// Logout and invalidate session
$logoutRequest = new LogoutRequest();
$logoutRequest->setSessionId('session-to-invalidate');

$client->Logout($logoutRequest, $metadata)->wait();
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

#### Using IAPService

```php
<?php

use Iap\IAPServiceClient;
use Iap\CalculateBasketRequest;
use Iap\BasketPosition;
use Iap\PlaceOrderRequest;
use Iap\GetInAppFeatureRequest;

$client = new IAPServiceClient($endpoint, $options);

$metadata = [
    'x-request-service' => ['my-php-service'],
    'x-request-id' => [uniqid('req-')],
];

// Calculate basket prices
$position1 = new BasketPosition();
$position1->setFeatureId('feature-123');
$position1->setQuantity(1);

$position2 = new BasketPosition();
$position2->setFeatureId('feature-456');
$position2->setQuantity(2);

$basketRequest = new CalculateBasketRequest();
$basketRequest->setPositions([$position1, $position2]);

[$basketResponse, $status] = $client->CalculateBasket($basketRequest, $metadata)->wait();

if ($status->code === \Grpc\STATUS_OK) {
    echo "Total: " . $basketResponse->getTotalPrice() . PHP_EOL;
}

// Get feature details
$featureRequest = new GetInAppFeatureRequest();
$featureRequest->setFeatureId('feature-123');

[$featureResponse, $status] = $client->GetInAppFeature($featureRequest, $metadata)->wait();

// Place an order
$orderRequest = new PlaceOrderRequest();
$orderRequest->setPositions([$position1]);
$orderRequest->setPaymentMethodId('payment-method-id');

[$orderResponse, $status] = $client->PlaceOrder($orderRequest, $metadata)->wait();
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

#### Using AuthService

```typescript
import { AuthServiceDefinition } from '@shopware/transaction-gateway-client/auth/auth';
import { createChannel, createClient, Metadata } from 'nice-grpc';

const channel = createChannel('transaction-gateway.example.com:443');
const authClient = createClient(AuthServiceDefinition, channel);

const metadata = createRequestMetadata('my-ts-service', `req-${Date.now()}`);

// Exchange OAuth code for tokens
async function exchangeCode(code: string, sessionId: string) {
    try {
        const response = await authClient.exchangeCode(
            { code, sessionId },
            { metadata }
        );
        
        console.log('Access Token:', response.accessToken);
        console.log('Session ID:', response.sessionId);
        return response;
    } catch (error) {
        console.error('Exchange code failed:', error);
        throw error;
    }
}

// Refresh token
async function refreshToken(sessionId: string) {
    const response = await authClient.refreshToken(
        { sessionId },
        { metadata }
    );
    return response;
}

// Get user account info
async function getUserAccount(accessToken: string) {
    const response = await authClient.getUserAccount(
        { accessToken },
        { metadata }
    );
    
    console.log('User ID:', response.userId);
    console.log('Email:', response.email);
    return response;
}

// Logout
async function logout(sessionId: string) {
    await authClient.logout(
        { sessionId },
        { metadata }
    );
    console.log('Logged out successfully');
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

#### Using IAPService

```typescript
import { IAPServiceDefinition, BasketPosition } from '@shopware/transaction-gateway-client/iap/iap';
import { createChannel, createClient, Metadata } from 'nice-grpc';

const channel = createChannel('transaction-gateway.example.com:443');
const iapClient = createClient(IAPServiceDefinition, channel);

const metadata = new Metadata();
metadata.set('x-request-service', 'my-ts-service');

// Calculate basket
async function calculateBasket(positions: BasketPosition[]) {
    const response = await iapClient.calculateBasket(
        { positions },
        { metadata }
    );
    
    console.log('Total Price:', response.totalPrice);
    return response;
}

// Get feature details
async function getFeature(featureId: string) {
    const response = await iapClient.getInAppFeature(
        { featureId },
        { metadata }
    );
    return response;
}

// Place order
async function placeOrder(positions: BasketPosition[], paymentMethodId: string) {
    const response = await iapClient.placeOrder(
        { positions, paymentMethodId },
        { metadata }
    );
    return response;
}

// Usage
calculateBasket([
    { featureId: 'feature-123', quantity: 1 },
    { featureId: 'feature-456', quantity: 2 },
]);
```

#### Error Handling

```typescript
import { ClientError, Status } from 'nice-grpc';

async function safePurchasesCall(domain: string) {
    try {
        return await purchasesClient.getPurchasesByDomain(
            { domain },
            { metadata }
        );
    } catch (error) {
        if (error instanceof ClientError) {
            switch (error.code) {
                case Status.NOT_FOUND:
                    console.error('Domain not found');
                    break;
                case Status.PERMISSION_DENIED:
                    console.error('Access denied');
                    break;
                case Status.UNAVAILABLE:
                    console.error('Service unavailable, retry later');
                    break;
                default:
                    console.error(`gRPC error (${error.code}): ${error.message}`);
            }
        }
        throw error;
    }
}
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

#### Using AuthService

```go
package main

import (
    "context"
    "fmt"
    "log"

    authpb "github.com/shopware/transaction-gateway-protos/auth"
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

    client := authpb.NewAuthServiceClient(conn)
    ctx := createContext("my-go-service", "req-12345")

    // Exchange OAuth code for tokens
    exchangeResp, err := client.ExchangeCode(ctx, &authpb.ExchangeCodeRequest{
        Code:      "authorization_code_from_oauth",
        SessionId: "unique-session-id",
    })
    if err != nil {
        log.Fatalf("ExchangeCode failed: %v", err)
    }
    fmt.Printf("Access Token: %s\n", exchangeResp.AccessToken)
    fmt.Printf("Session ID: %s\n", exchangeResp.SessionId)

    // Refresh token
    refreshResp, err := client.RefreshToken(ctx, &authpb.RefreshTokenRequest{
        SessionId: exchangeResp.SessionId,
    })
    if err != nil {
        log.Fatalf("RefreshToken failed: %v", err)
    }
    fmt.Printf("New Access Token: %s\n", refreshResp.AccessToken)

    // Get user account info
    userResp, err := client.GetUserAccount(ctx, &authpb.GetUserAccountRequest{
        AccessToken: exchangeResp.AccessToken,
    })
    if err != nil {
        log.Fatalf("GetUserAccount failed: %v", err)
    }
    fmt.Printf("User ID: %s\n", userResp.UserId)
    fmt.Printf("Email: %s\n", userResp.Email)

    // Logout
    _, err = client.Logout(ctx, &authpb.LogoutRequest{
        SessionId: exchangeResp.SessionId,
    })
    if err != nil {
        log.Fatalf("Logout failed: %v", err)
    }
    fmt.Println("Logged out successfully")
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

#### Using IAPService

```go
package main

import (
    "context"
    "fmt"
    "log"

    iappb "github.com/shopware/transaction-gateway-protos/iap"
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

    client := iappb.NewIAPServiceClient(conn)
    ctx := metadata.AppendToOutgoingContext(
        context.Background(),
        "x-request-service", "my-go-service",
    )

    // Calculate basket
    basketResp, err := client.CalculateBasket(ctx, &iappb.CalculateBasketRequest{
        Positions: []*iappb.BasketPosition{
            {FeatureId: "feature-123", Quantity: 1},
            {FeatureId: "feature-456", Quantity: 2},
        },
    })
    if err != nil {
        log.Fatalf("CalculateBasket failed: %v", err)
    }
    fmt.Printf("Total Price: %v\n", basketResp.TotalPrice)

    // Get feature details
    featureResp, err := client.GetInAppFeature(ctx, &iappb.GetInAppFeatureRequest{
        FeatureId: "feature-123",
    })
    if err != nil {
        log.Fatalf("GetInAppFeature failed: %v", err)
    }
    fmt.Printf("Feature: %+v\n", featureResp)

    // Place order
    orderResp, err := client.PlaceOrder(ctx, &iappb.PlaceOrderRequest{
        Positions: []*iappb.BasketPosition{
            {FeatureId: "feature-123", Quantity: 1},
        },
        PaymentMethodId: "payment-method-id",
    })
    if err != nil {
        log.Fatalf("PlaceOrder failed: %v", err)
    }
    fmt.Printf("Order placed: %+v\n", orderResp)
}
```

#### Error Handling

```go
package main

import (
    "context"
    "log"

    purchasespb "github.com/shopware/transaction-gateway-protos/purchases"
    "google.golang.org/grpc/codes"
    "google.golang.org/grpc/status"
)

func getPurchasesSafe(client purchasespb.PurchasesServiceClient, ctx context.Context, domain string) (*purchasespb.GetPurchasesByDomainResponse, error) {
    resp, err := client.GetPurchasesByDomain(ctx, &purchasespb.GetPurchasesByDomainRequest{
        Domain: domain,
    })
    if err != nil {
        st, ok := status.FromError(err)
        if ok {
            switch st.Code() {
            case codes.NotFound:
                log.Printf("Domain not found: %s", domain)
            case codes.PermissionDenied:
                log.Printf("Access denied for domain: %s", domain)
            case codes.Unavailable:
                log.Printf("Service unavailable, retry later")
            case codes.InvalidArgument:
                log.Printf("Invalid argument: %s", st.Message())
            default:
                log.Printf("gRPC error (%s): %s", st.Code(), st.Message())
            }
        }
        return nil, err
    }
    return resp, nil
}
```

---

## Available Services

### AuthService

| RPC               | Description                                                  |
|-------------------|--------------------------------------------------------------|
| `ExchangeCode`    | Exchanges an OAuth authorization code for access/refresh tokens |
| `GetTokenForSession` | Retrieves tokens for an existing session                   |
| `RefreshToken`    | Refreshes an expired access token                            |
| `Logout`          | Invalidates user session and removes stored tokens           |
| `GetUserAccount`  | Fetches OIDC userinfo for an access token                    |

### IAPService

| RPC               | Description                                         |
|-------------------|-----------------------------------------------------|
| `CalculateBasket` | Calculates prices and taxes for a basket            |
| `PlaceOrder`      | Places an order for in-app purchases                |
| `GetInAppFeature` | Retrieves details about a specific in-app feature   |

### PurchasesService

| RPC                    | Description                                          |
|------------------------|------------------------------------------------------|
| `GetPurchasesByDomain` | Retrieves all purchases for a specific shop domain   |

---

## Required Metadata Headers

All requests **must** include these metadata headers:

| Header              | Required | Description                                           |
|---------------------|----------|-------------------------------------------------------|
| `x-request-service` | No       | Service name for log correlation (e.g., "my-service") |
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

`
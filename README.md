# Transaction Gateway Client Usage Guide

This guide provides detailed instructions for installing and using the Transaction Gateway gRPC clients in your applications.

## Table of Contents

- [Overview](#overview)
- [PHP Example](#php-example)
- [TypeScript Example](#typescript-example)
- [Go Example](#go-example)
- [Available Services](#available-services)

---

## Overview

The Transaction Gateway exposes gRPC services for authentication, in-app purchases, and purchase data retrieval. Generated client libraries are distributed via Git tags:

| Language   | Tag          | Package Manager |
|------------|--------------|-----------------|
| PHP        | `1.0.0+php`  | Composer        |
| TypeScript | `1.0.0+ts`   | npm/yarn        |
| Go         | `v1.0.0`     | Go Modules      |

All clients are published to: `github.com/shopware/transaction-gateway-protos`

---

## PHP Example

See the complete working example in [`examples/php/`](examples/php/).

---

## TypeScript Example

See the complete working example in [`examples/ts/`](examples/ts/).

---

## Go Example

See the complete working example in [`examples/go/`](examples/go/).

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

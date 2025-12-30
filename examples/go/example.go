/**
 * Example usage of Transaction Gateway gRPC client in Go.
 *
 * Prerequisites:
 * 1. Go 1.25+
 * 2. Ensure gRPC server is running on localhost:50051
 * 3. go mod tidy && go run example.go
 */
package main

import (
	"context"
	"fmt"
	"strings"

	purchasesv1 "github.com/shopware/transaction-gateway-protos/purchases/v1"
	"google.golang.org/grpc"
	"google.golang.org/grpc/codes"
	"google.golang.org/grpc/credentials/insecure"
	"google.golang.org/grpc/metadata"
	"google.golang.org/grpc/status"
)

const (
	// Gateway endpoint for local development
	endpoint = "localhost:50051"
)

// TestGetPurchasesByDomain demonstrates how to use the PurchasesService
func main() {
	fmt.Println(strings.Repeat("=", 50))
	fmt.Println("Transaction Gateway Go Example")
	fmt.Println(strings.Repeat("=", 50))

	// Create insecure connection for local development
	conn, err := grpc.NewClient(endpoint,
		grpc.WithTransportCredentials(insecure.NewCredentials()))
	if err != nil {
		fmt.Printf("❌ Failed to connect: %v", err)
		return
	}
	defer conn.Close()

	// Create the client
	client := purchasesv1.NewPurchasesServiceClient(conn)

	// Set required metadata headers
	ctx := metadata.AppendToOutgoingContext(context.Background(),
		"x-request-service", "example-go-test",
		"x-request-id", "test-request-001", // Optional
	)

	// Make the request
	resp, err := client.GetPurchasesByDomain(ctx,
		&purchasesv1.GetPurchasesByDomainRequest{
			Domain: "example-shop.com",
		})

	if err != nil {
		// Check if it's a NotFound error (expected for test domain)
		st, ok := status.FromError(err)
		if ok && st.Code() == codes.NotFound {
			fmt.Printf("✅ Connection successful - got expected NotFound for test domain\n")
			fmt.Printf("❌ Error: %s\n", st.Message())
			return
		}
		fmt.Printf("❌ GetPurchasesByDomain failed: %v", err)
		return
	}

	// Print results if purchases exist
	fmt.Printf("✅ Found %d purchases:\n", len(resp.Purchases))
	for _, p := range resp.Purchases {
		fmt.Printf("  Plugin: %s, Identifier: %s, Quantity: %d\n",
			p.PluginName, p.Identifier, p.Quantity)
	}
}

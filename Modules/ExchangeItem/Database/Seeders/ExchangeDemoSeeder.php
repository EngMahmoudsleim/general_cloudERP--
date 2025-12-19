<?php

namespace Modules\ExchangeItem\Database\Seeders;

use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\Product;
use App\Transaction;
use App\TransactionSellLine;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\ExchangeItem\Entities\TransactionExchange;
use Modules\ExchangeItem\Entities\TransactionExchangeLine;

class ExchangeDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        try {
            // Get first business and required data
            $business = Business::first();
            if (!$business) {
                $this->command->error('No business found. Please create a business first.');
                return;
            }

            $location = $business->locations()->first();
            if (!$location) {
                $this->command->error('No business location found.');
                return;
            }

            $user = User::where('business_id', $business->id)->first();
            if (!$user) {
                $this->command->error('No user found for this business.');
                return;
            }

            // Get or create demo products
            $products = $this->getOrCreateDemoProducts($business, $user);

            if ($products->count() < 2) {
                $this->command->error('Failed to create demo products.');
                return;
            }

            // Get or create a customer
            $customer = Contact::where('business_id', $business->id)
                ->where('type', 'customer')
                ->first();

            if (!$customer) {
                $this->command->error('No customer found. Please add at least one customer.');
                return;
            }

            $this->command->info('Creating demo exchange transactions...');
            $this->command->info('Business: ' . $business->name);
            $this->command->info('Location: ' . $location->name);
            $this->command->info('Customer: ' . $customer->name);

            // Create demo exchanges
            $this->createExchangeScenario1($business, $location, $user, $customer, $products);
            $this->createExchangeScenario2($business, $location, $user, $customer, $products);
            $this->createExchangeScenario3($business, $location, $user, $customer, $products);

            DB::commit();

            $this->command->info('✓ Exchange demo data created successfully!');
            $this->command->info('✓ Created 3 exchange scenarios with different types');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error creating demo data: ' . $e->getMessage());
            $this->command->error('Line: ' . $e->getLine());
            $this->command->error('File: ' . $e->getFile());
        }
    }

    /**
     * Scenario 1: Customer exchanges item for more expensive item (pays difference)
     */
    private function createExchangeScenario1($business, $location, $user, $customer, $products)
    {
        $this->command->info('Creating Scenario 1: Customer pays difference...');

        // Original product and new product
        $originalProduct = $products[0];
        $newProduct = $products[1];

        // Create original sale transaction
        $originalTransaction = $this->createSaleTransaction(
            $business,
            $location,
            $user,
            $customer,
            $originalProduct,
            1,
            100.00, // Original price
            Carbon::now()->subDays(5)
        );

        // Create new sale transaction (exchange transaction)
        $exchangeTransaction = $this->createSaleTransaction(
            $business,
            $location,
            $user,
            $customer,
            $newProduct,
            1,
            150.00, // New higher price
            Carbon::now()->subDays(2)
        );

        // Create exchange record
        $exchange = TransactionExchange::create([
            'business_id' => $business->id,
            'location_id' => $location->id,
            'original_transaction_id' => $originalTransaction->id,
            'exchange_transaction_id' => $exchangeTransaction->id,
            'exchange_ref_no' => TransactionExchange::generateExchangeRefNo($business->id),
            'exchange_date' => Carbon::now()->subDays(2),
            'original_amount' => 100.00,
            'new_amount' => 150.00,
            'exchange_difference' => 50.00,
            'payment_received' => 50.00,
            'refund_given' => 0.00,
            'total_exchange_amount' => 50.00,
            'status' => 'completed',
            'created_by' => $user->id,
            'notes' => 'Customer upgraded to a more expensive item',
        ]);

        // Create exchange line
        TransactionExchangeLine::create([
            'exchange_id' => $exchange->id,
            'original_sell_line_id' => $originalTransaction->sell_lines->first()->id,
            'new_sell_line_id' => $exchangeTransaction->sell_lines->first()->id,
            'exchange_type' => 'exchange',
            'original_quantity' => 1,
            'original_unit_price' => 100.00,
            'new_quantity' => 1,
            'new_unit_price' => 150.00,
            'price_difference' => 50.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('  ✓ Scenario 1 created: ' . $exchange->exchange_ref_no);
    }

    /**
     * Scenario 2: Customer exchanges item for cheaper item (gets refund)
     */
    private function createExchangeScenario2($business, $location, $user, $customer, $products)
    {
        $this->command->info('Creating Scenario 2: Customer gets refund...');

        $originalProduct = $products[2];
        $newProduct = $products[3];

        // Create original sale
        $originalTransaction = $this->createSaleTransaction(
            $business,
            $location,
            $user,
            $customer,
            $originalProduct,
            1,
            200.00,
            Carbon::now()->subDays(7)
        );

        // Create exchange sale
        $exchangeTransaction = $this->createSaleTransaction(
            $business,
            $location,
            $user,
            $customer,
            $newProduct,
            1,
            120.00,
            Carbon::now()->subDays(3)
        );

        // Create exchange record
        $exchange = TransactionExchange::create([
            'business_id' => $business->id,
            'location_id' => $location->id,
            'original_transaction_id' => $originalTransaction->id,
            'exchange_transaction_id' => $exchangeTransaction->id,
            'exchange_ref_no' => TransactionExchange::generateExchangeRefNo($business->id),
            'exchange_date' => Carbon::now()->subDays(3),
            'original_amount' => 200.00,
            'new_amount' => 120.00,
            'exchange_difference' => -80.00,
            'payment_received' => 0.00,
            'refund_given' => 80.00,
            'total_exchange_amount' => -80.00,
            'status' => 'completed',
            'created_by' => $user->id,
            'notes' => 'Customer downgraded to a less expensive item',
        ]);

        TransactionExchangeLine::create([
            'exchange_id' => $exchange->id,
            'original_sell_line_id' => $originalTransaction->sell_lines->first()->id,
            'new_sell_line_id' => $exchangeTransaction->sell_lines->first()->id,
            'exchange_type' => 'exchange',
            'original_quantity' => 1,
            'original_unit_price' => 200.00,
            'new_quantity' => 1,
            'new_unit_price' => 120.00,
            'price_difference' => -80.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('  ✓ Scenario 2 created: ' . $exchange->exchange_ref_no);
    }

    /**
     * Scenario 3: Even exchange (same value items)
     */
    private function createExchangeScenario3($business, $location, $user, $customer, $products)
    {
        $this->command->info('Creating Scenario 3: Even exchange...');

        $originalProduct = $products[4];
        $newProduct = $products[0];

        // Create original sale
        $originalTransaction = $this->createSaleTransaction(
            $business,
            $location,
            $user,
            $customer,
            $originalProduct,
            1,
            75.00,
            Carbon::now()->subDays(10)
        );

        // Create exchange sale
        $exchangeTransaction = $this->createSaleTransaction(
            $business,
            $location,
            $user,
            $customer,
            $newProduct,
            1,
            75.00,
            Carbon::now()->subDay()
        );

        // Create exchange record
        $exchange = TransactionExchange::create([
            'business_id' => $business->id,
            'location_id' => $location->id,
            'original_transaction_id' => $originalTransaction->id,
            'exchange_transaction_id' => $exchangeTransaction->id,
            'exchange_ref_no' => TransactionExchange::generateExchangeRefNo($business->id),
            'exchange_date' => Carbon::now()->subDay(),
            'original_amount' => 75.00,
            'new_amount' => 75.00,
            'exchange_difference' => 0.00,
            'payment_received' => 0.00,
            'refund_given' => 0.00,
            'total_exchange_amount' => 0.00,
            'status' => 'completed',
            'created_by' => $user->id,
            'notes' => 'Even exchange - same value items',
        ]);

        TransactionExchangeLine::create([
            'exchange_id' => $exchange->id,
            'original_sell_line_id' => $originalTransaction->sell_lines->first()->id,
            'new_sell_line_id' => $exchangeTransaction->sell_lines->first()->id,
            'exchange_type' => 'exchange',
            'original_quantity' => 1,
            'original_unit_price' => 75.00,
            'new_quantity' => 1,
            'new_unit_price' => 75.00,
            'price_difference' => 0.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('  ✓ Scenario 3 created: ' . $exchange->exchange_ref_no);
    }

    /**
     * Helper method to create a sale transaction
     */
    private function createSaleTransaction($business, $location, $user, $customer, $product, $quantity, $price, $date)
    {
        // Generate invoice number
        $invoiceCount = Transaction::where('business_id', $business->id)
            ->where('type', 'sell')
            ->count() + 1;
        $invoiceNo = $business->invoice_scheme_id
            ? 'DEMO-' . str_pad($invoiceCount, 6, '0', STR_PAD_LEFT)
            : str_pad($invoiceCount, 6, '0', STR_PAD_LEFT);

        // Create transaction
        $transaction = Transaction::create([
            'business_id' => $business->id,
            'location_id' => $location->id,
            'type' => 'sell',
            'status' => 'final',
            'contact_id' => $customer->id,
            'invoice_no' => $invoiceNo,
            'transaction_date' => $date,
            'total_before_tax' => $price * $quantity,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'final_total' => $price * $quantity,
            'payment_status' => 'paid',
            'created_by' => $user->id,
        ]);

        // Get the first variation
        $variation = $product->variations()->first();

        // Create transaction sell line
        TransactionSellLine::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'variation_id' => $variation ? $variation->id : null,
            'quantity' => $quantity,
            'unit_price_before_discount' => $price,
            'unit_price' => $price,
            'line_discount_type' => 'fixed',
            'line_discount_amount' => 0,
            'unit_price_inc_tax' => $price,
            'tax_id' => null,
            'item_tax' => 0,
        ]);

        return $transaction->fresh(['sell_lines']);
    }

    /**
     * Get or create demo products for exchange testing
     */
    private function getOrCreateDemoProducts($business, $user)
    {
        // Check if we have enough products
        $existingProducts = Product::where('business_id', $business->id)
            ->where('type', 'single')
            ->get();

        if ($existingProducts->count() >= 5) {
            return $existingProducts->take(5);
        }

        $this->command->info('Creating demo products for exchange testing...');

        $demoProducts = [
            ['name' => 'Demo Laptop - HP Pavilion', 'sku' => 'DEMO-LP-001', 'price' => 899.99],
            ['name' => 'Demo Laptop - Dell Inspiron', 'sku' => 'DEMO-LP-002', 'price' => 1299.99],
            ['name' => 'Demo Phone - iPhone 13', 'sku' => 'DEMO-PH-001', 'price' => 799.99],
            ['name' => 'Demo Phone - Samsung S22', 'sku' => 'DEMO-PH-002', 'price' => 699.99],
            ['name' => 'Demo Tablet - iPad Air', 'sku' => 'DEMO-TB-001', 'price' => 599.99],
        ];

        $products = collect();

        // Get or create category
        $category = \App\Category::firstOrCreate(
            [
                'business_id' => $business->id,
                'name' => 'Demo Electronics'
            ],
            [
                'short_code' => 'DEMO-ELEC',
                'created_by' => $user->id
            ]
        );

        // Get or create brand
        $brand = \App\Brands::firstOrCreate(
            [
                'business_id' => $business->id,
                'name' => 'Demo Brand'
            ],
            [
                'created_by' => $user->id
            ]
        );

        // Get or create unit
        $unit = \App\Unit::firstOrCreate(
            [
                'business_id' => $business->id,
                'actual_name' => 'Piece'
            ],
            [
                'short_name' => 'Pc',
                'allow_decimal' => 0,
                'created_by' => $user->id
            ]
        );

        // Get tax rate (or null if none exists)
        $taxRate = \App\TaxRate::where('business_id', $business->id)->first();

        foreach ($demoProducts as $index => $productData) {
            // Check if product already exists
            $existing = Product::where('business_id', $business->id)
                ->where('sku', $productData['sku'])
                ->first();

            if ($existing) {
                $products->push($existing);
                continue;
            }

            // Create product
            $product = Product::create([
                'name' => $productData['name'],
                'business_id' => $business->id,
                'type' => 'single',
                'unit_id' => $unit->id,
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'tax' => $taxRate ? $taxRate->id : null,
                'sku' => $productData['sku'],
                'enable_stock' => 1,
                'alert_quantity' => 5,
                'created_by' => $user->id,
            ]);

            // Create product variation template first
            $productVariation = \App\ProductVariation::create([
                'product_id' => $product->id,
                'name' => 'DUMMY',
                'variation_template_id' => null,
            ]);

            // Create variation linked to product variation
            $variation = \App\Variation::create([
                'name' => 'DUMMY',
                'product_id' => $product->id,
                'sub_sku' => $productData['sku'],
                'product_variation_id' => $productVariation->id,
                'default_purchase_price' => $productData['price'] * 0.6, // 60% cost
                'dpp_inc_tax' => $productData['price'] * 0.6,
                'profit_percent' => 40,
                'default_sell_price' => $productData['price'],
                'sell_price_inc_tax' => $productData['price'],
            ]);

            $products->push($product->fresh(['variations']));
            $this->command->info('  ✓ Created product: ' . $productData['name']);
        }

        return $products;
    }
}

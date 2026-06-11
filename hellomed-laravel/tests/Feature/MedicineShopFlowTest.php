<?php

namespace Tests\Feature;

use App\Models\Medicine;
use App\Models\MedicineOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MedicineShopFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_pharmacist_can_access_pharmacy_dashboard(): void
    {
        $user = User::query()->create([
            'name' => 'Pharmacist User',
            'email' => 'pharma@example.com',
            'password' => Hash::make('password123'),
            'role' => 'pharmacist',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get('/pharmacist')
            ->assertOk();
    }

    public function test_patient_can_buy_medicine_from_cart_checkout(): void
    {
        $patient = User::query()->create([
            'name' => 'Medicine Buyer',
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        $medicine = Medicine::query()->create([
            'name' => 'Test Medicine',
            'medicine_group' => 'Paracetamol',
            'description' => 'Test desc',
            'power' => '10mg',
            'amount' => '1 strip x 10 tablets',
            'strength' => '10mg',
            'manufacturer' => 'Test Pharma',
            'price' => 25,
            'stock_quantity' => 20,
            'is_active' => true,
            'requires_prescription' => false,
        ]);

        $this->post('/shop/cart/'.$medicine->id, [
            'quantity' => 2,
        ])->assertRedirect();

        $this->actingAs($patient)
            ->post('/shop/checkout', [
                'delivery_address' => '123 Test Street',
                'phone' => '01755555555',
                'payment_method' => 'cash-on-delivery',
                'notes' => 'Deliver evening',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('medicine_orders', [
            'user_id' => $patient->id,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('medicine_order_items', [
            'medicine_id' => $medicine->id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('medicines', [
            'id' => $medicine->id,
            'stock_quantity' => 18,
        ]);
    }

    public function test_prescription_is_required_for_rx_medicine_checkout(): void
    {
        Storage::fake('public');

        $patient = User::query()->create([
            'name' => 'Rx Buyer',
            'email' => 'rxbuyer@example.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        $medicine = Medicine::query()->create([
            'name' => 'Prescription Medicine',
            'medicine_group' => 'Antibiotic',
            'description' => 'Needs Rx',
            'power' => '5mg',
            'amount' => '1 strip x 10 tablets',
            'strength' => '5mg',
            'manufacturer' => 'Rx Pharma',
            'price' => 50,
            'stock_quantity' => 10,
            'is_active' => true,
            'requires_prescription' => true,
        ]);

        $this->post('/shop/cart/'.$medicine->id, ['quantity' => 1])->assertRedirect();

        $this->actingAs($patient)
            ->post('/shop/checkout', [
                'delivery_address' => 'Rx Street',
                'phone' => '01711111111',
                'payment_method' => 'cash-on-delivery',
            ])
            ->assertSessionHasErrors('prescription');

        $this->actingAs($patient)
            ->post('/shop/checkout', [
                'delivery_address' => 'Rx Street',
                'phone' => '01711111111',
                'payment_method' => 'cash-on-delivery',
                'prescription' => UploadedFile::fake()->create('rx.pdf', 50, 'application/pdf'),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('medicine_orders', [
            'user_id' => $patient->id,
            'contains_prescription_items' => true,
        ]);
    }

    public function test_patient_can_complete_mock_bkash_payment_for_medicine_order(): void
    {
        $patient = User::query()->create([
            'name' => 'Gateway Buyer',
            'email' => 'gatewaybuyer@example.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        $order = MedicineOrder::query()->create([
            'user_id' => $patient->id,
            'order_number' => 'MED-TEST-001',
            'status' => 'pending',
            'total_amount' => 100,
            'payment_method' => 'bkash',
            'payment_callback_token' => 'token-123',
            'payment_status' => 'pending',
            'delivery_address' => 'Road 1',
            'phone' => '01722222222',
        ]);

        $this->actingAs($patient)
            ->get('/shop/payments/'.$order->id.'/bkash/callback/success?token=token-123')
            ->assertRedirect('/my/medicine-orders/'.$order->id);

        $this->assertDatabaseHas('medicine_orders', [
            'id' => $order->id,
            'payment_status' => 'paid',
        ]);
    }

    public function test_online_payment_commits_inventory_only_after_success_callback(): void
    {
        $patient = User::query()->create([
            'name' => 'Delayed Commit Buyer',
            'email' => 'delayed-commit@example.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        $medicine = Medicine::query()->create([
            'name' => 'Deferred Stock Medicine',
            'medicine_group' => 'Vitamin',
            'description' => 'Deferred stock commit',
            'power' => '250mg',
            'amount' => '1 box',
            'strength' => '250mg',
            'manufacturer' => 'StockSafe Pharma',
            'price' => 80,
            'stock_quantity' => 10,
            'is_active' => true,
            'requires_prescription' => false,
        ]);

        $this->post('/shop/cart/'.$medicine->id, ['quantity' => 2])->assertRedirect();

        $this->actingAs($patient)
            ->post('/shop/checkout', [
                'delivery_address' => 'Online Pay Road',
                'phone' => '01731313131',
                'payment_method' => 'bkash',
            ])
            ->assertRedirect();

        $order = MedicineOrder::query()->latest()->first();
        $this->assertNotNull($order);

        $this->assertDatabaseHas('medicines', [
            'id' => $medicine->id,
            'stock_quantity' => 10,
        ]);

        $this->actingAs($patient)
            ->get('/shop/payments/'.$order->id.'/bkash/callback/success?token='.$order->payment_callback_token)
            ->assertRedirect('/my/medicine-orders/'.$order->id);

        $this->assertDatabaseHas('medicines', [
            'id' => $medicine->id,
            'stock_quantity' => 8,
        ]);
    }

    public function test_pharmacist_can_open_prescription_file_via_route(): void
    {
        Storage::fake('public');

        Storage::disk('public')->put('prescriptions/sample-prescription.pdf', 'PDF CONTENT');

        $pharmacist = User::query()->create([
            'name' => 'Prescription Pharmacist',
            'email' => 'rx-pharmacist@example.com',
            'password' => Hash::make('password123'),
            'role' => 'pharmacist',
            'is_active' => true,
        ]);

        $patient = User::query()->create([
            'name' => 'Prescription Patient',
            'email' => 'rx-patient@example.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        $order = MedicineOrder::query()->create([
            'user_id' => $patient->id,
            'order_number' => 'MED-RX-ROUTE-001',
            'status' => 'pending',
            'total_amount' => 120,
            'payment_method' => 'cash-on-delivery',
            'payment_status' => 'pending',
            'delivery_address' => 'Road 12',
            'phone' => '01740000000',
            'prescription_path' => 'prescriptions/sample-prescription.pdf',
            'contains_prescription_items' => true,
        ]);

        $this->actingAs($pharmacist)
            ->get('/pharmacist/orders/'.$order->id.'/prescription')
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}

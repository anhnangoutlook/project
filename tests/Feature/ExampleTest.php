<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_leaf_products_view_renders_weight_values_from_strings(): void
    {
        $productNames = ['Raw'];
        $leafProducts = new LengthAwarePaginator([
            (object) [
                'category' => 'Raw',
                'name' => 'Test product',
                'code_from' => 'A1',
                'code_to' => 'A2',
                'total_qty' => '10',
                'status_1' => '2',
                'remain_qty' => '8',
                'total_weight' => '12.3',
                'status_2' => '1.2',
                'remain_weight' => '11.1',
                'main_id' => 1,
                'id' => 1,
            ],
        ], 1, 15, 1, ['path' => '/']);

        $html = view('leaf-products', compact('leafProducts', 'productNames'))->render();

        $this->assertStringContainsString('12g', $html);
    }

    public function test_stock_removal_table_view_renders_weight_values_from_strings(): void
    {
        $productNames = ['Raw'];
        $leafProducts = new LengthAwarePaginator([
            [
                'category' => 'Raw',
                'name' => 'Test product',
                'code_from' => 'A1',
                'code_to' => 'A2',
                'total_qty' => '10',
                'status_1' => '2',
                'remain_qty' => '8',
                'total_weight' => '12.3',
                'status_2' => '1.2',
                'remain_weight' => '11.1',
                'main_id' => 1,
                'id' => 1,
            ],
        ], 1, 15, 1, ['path' => '/']);

        $monthKey = '2026-07';
        $snapshots = collect();

        $html = view('stock-removal-table', compact('leafProducts', 'productNames', 'monthKey', 'snapshots'))->render();

        $this->assertStringContainsString('12g', $html);
    }
}

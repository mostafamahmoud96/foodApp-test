<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\Ingredient;
use App\Mail\IngredientAlertMail;
use Database\Seeders\AdminSeeder;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\Sequence;


uses(RefreshDatabase::class);
const END_POINT = 'api/orders';
const HEADERS = ['Accept' => 'application/json'];

beforeEach(function () {

    $this->product = Product::factory()->create([
        'name' => 'Burger',
    ]);

    $this->seed(AdminSeeder::class);

    // 6 of product one requires 600 gm of beef and 300 gm of tomato and 120 gm of onion
    // 4 of product one requires 400 gm of beef and 200 gm of tomato and 80 gm of onion

    Ingredient::factory()->count(3)->state(new Sequence(
        [
            'name'  => 'Beef',
            'stock' => 500,
            'level' => 500,
        ],
        [
            'name'  => 'Tomato',
            'stock' => 250,
            'level' => 250,
        ],
        [
            'name' => 'Onion',
            'stock' => 100,
            'level' => 100,
        ]
    ))->create();

    $this->product->ingredients()->sync([
        1 => [
            'amount' => 100,
        ],
        2 => [
            'amount' => 50,
        ],
        3 => [
            'amount' => 20,
        ],
    ]);
});

it('validates input data correctly', function () {
    $response = $this->post(END_POINT, [
        'products' => [
            [
                'product_id' => 1,
            ],
            [
                'product_id' => 100,
                'quantity' => 0,
            ],
        ],
    ], HEADERS);

    $response
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors([
            'products.1.product_id' => 'The selected products.1.product_id is invalid.',
            'products.0.quantity' => 'The products.0.quantity field is required.',
            'products.1.quantity' => 'The products.1.quantity field must be at least 1.',
        ], 'errors');
});

it('handles insufficient ingredients', function () {
    $response = $this->post(END_POINT, [
        'products' => [
            [
                'product_id' => 1,
                'quantity' => 6,
            ],
        ],
    ], HEADERS);

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment([
            'success' => false,
            'message' => 'Insufficient ingredients amount to complete the order',
            'errors'  => [
                [
                    'Ingredient Beef requires more amount which is 100 GM for 6 Burger',
                    'Ingredient Tomato requires more amount which is 50 GM for 6 Burger',
                    'Ingredient Onion requires more amount which is 20 GM for 6 Burger',
                ],
            ],
        ]);
});

it('creates an order successfully', function () {

    $response = $this->post(END_POINT, [
        'products' => [
            [
                'product_id' => 1,
                'quantity' => 5,
            ],
        ],
    ], HEADERS);


    $order = Order::query()->first();
    $this->assertDatabaseCount('orders', 1);
    $this->assertDatabaseHas('order_product', [
        'order_id'   => $order->id,
        'product_id' => $this->product->id,
        'quantity'   => 5,
    ]);

    $this->assertDatabaseHas('ingredients', [
        'name'  => 'Beef',
        'level' => 0,
    ]);

    $this->assertDatabaseHas('ingredients', [
        'name'  => 'Tomato',
        'level' => 0,
    ]);

    $this->assertDatabaseHas('ingredients', [
        'name'  => 'Onion',
        'level' => 0,
    ]);

    $this->assertDatabaseHas('ingredient_alerts', [
        'ingredient_id' => 1,
    ]);

    $this->assertDatabaseHas('ingredient_alerts', [
        'ingredient_id' => 2,
    ]);

    $this->assertDatabaseHas('ingredient_alerts', [
        'ingredient_id' => 3,
    ]);



    $response->assertStatus(Response::HTTP_CREATED)
        ->assertJsonFragment([
            'success' => true,
            'message' => 'Order is placed successfully!',
        ]);
});

it('queues an email after order creation', function () {
    Mail::fake();

    $this->post(END_POINT, [
        'products' => [
            [
                'product_id' => 1,
                'quantity' => 5,
            ],
        ],
    ], HEADERS);

    Mail::assertQueued(IngredientAlertMail::class, function ($mail) {
        return $mail->ingredients->count() == 3;
    });
});

it('does not queue an email if no ingredient is below 50%', function () {
    Mail::fake();

    $this->post(END_POINT, [
        'products' => [
            [
                'product_id' => 1,
                'quantity' => 1,
            ],
        ],
    ], HEADERS);

    Mail::assertNothingQueued();
});

it('does not queue an email if ingredient is already alerted', function () {
    Mail::fake();

    $this->post(END_POINT, [
        'products' => [
            [
                'product_id' => 1,
                'quantity' => 6,
            ],
        ],
    ], HEADERS);

    Mail::assertNothingQueued();
});



it('renders the ingredient alert email values correctly', function () {

    Mail::fake();

    $this->post(END_POINT, [
        'products' => [
            [
                'product_id' => 1,
                'quantity' => 4,
            ],
        ],
    ], HEADERS);

    $ingredients = collect([
        Ingredient::find(1),
        Ingredient::find(2),
    ]);

    $mailable = new IngredientAlertMail($ingredients);
    $rendered = $mailable->render();

    $this->assertStringContainsString('Ingredients Level Alert', $rendered);
    $this->assertStringContainsString('Beef', $rendered);
    $this->assertStringContainsString('Tomato', $rendered);
    $this->assertStringContainsString('20.00%', $rendered); // Beef percentage
    $this->assertStringContainsString('20.00%', $rendered); // Tomato percentage
});

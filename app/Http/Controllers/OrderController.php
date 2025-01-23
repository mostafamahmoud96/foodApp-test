<?php

namespace App\Http\Controllers;

use App\DTOs\CreateOrderDto;
use Illuminate\Http\Response;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\CreateOrderRequest;

class OrderController extends Controller
{
    /**
     * @param OrderService $orderService
     */
    public function __construct(public OrderService $orderService) {}

    /**
     * @param CreateOrderRequest $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function __invoke(CreateOrderRequest $request): JsonResponse
    {
        $createOrderDto = CreateOrderDto::from($request);
        $this->orderService->createOrder($createOrderDto);

        return response()->json([
            'success' => true,
            'message' => 'Order is placed successfully!',
        ], Response::HTTP_CREATED);
    }
}

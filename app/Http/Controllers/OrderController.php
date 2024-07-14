<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        try {
            // Validar la solicitud
            $validated = $request->validate([
                'pickup_location' => 'required|string',
                'dropoff_location' => 'required|string',
                'pickup_time' => 'required|date',
                'dropoff_time' => 'required|date',
                'fare' => 'required|numeric',
            ]);

            // Crear un nuevo pedido
            $order = new Order();
            $order->pickup_location = $validated['pickup_location'];
            $order->dropoff_location = $validated['dropoff_location'];
            $order->pickup_time = $validated['pickup_time'];
            $order->dropoff_time = $validated['dropoff_time'];
            $order->fare = $validated['fare'];
            $order->save();

            return response()->json(['message' => 'Pedido creado exitosamente'], 200);
        } catch (\Exception $e) {
            Log::error('Error al crear el pedido: ' . $e->getMessage());
            return response()->json(['message' => 'Error al crear el pedido', 'error' => $e->getMessage()], 500);
        }
    }
}

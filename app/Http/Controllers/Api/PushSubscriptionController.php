<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
            'auth_key' => 'required|string',
            'p256dh_key' => 'required|string',
        ]);

        $clienteId = session('cliente_id');
        if (!$clienteId) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $sub = PushSubscription::updateOrCreate(
            [
                'subscribable_id' => $clienteId,
                'subscribable_type' => \App\Models\Cliente::class,
            ],
            [
                'endpoint' => $request->endpoint,
                'auth_key' => $request->auth_key,
                'p256dh_key' => $request->p256dh_key,
            ]
        );

        return response()->json($sub);
    }

    public function destroy(Request $request)
    {
        $clienteId = session('cliente_id');
        if (!$clienteId) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        PushSubscription::where('subscribable_id', $clienteId)
            ->where('subscribable_type', \App\Models\Cliente::class)
            ->delete();

        return response()->json(['ok' => true]);
    }
}

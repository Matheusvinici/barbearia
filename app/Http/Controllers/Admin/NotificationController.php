<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::guard('web')->user() ?? Auth::guard('barbeiro')->user();
        $notificacoes = $user->notifications()->latest()->limit(10)->get()->map(function ($n) {
            $data = $n->data;
            return [
                'id' => $n->id,
                'title' => $data['title'] ?? 'Notificação',
                'message' => $data['message'] ?? '',
                'url' => $data['url'] ?? '#',
                'icon' => $data['icon'] ?? 'fas fa-info-circle',
                'color' => $data['color'] ?? '#6c757d',
                'ago' => $n->created_at->diffForHumans(),
                'lida' => !is_null($n->read_at),
            ];
        });

        return response()->json([
            'nao_lidas' => $user->unreadNotifications->count(),
            'notificacoes' => $notificacoes,
        ]);
    }

    public function marcarTodas()
    {
        $user = Auth::guard('web')->user() ?? Auth::guard('barbeiro')->user();
        $user->unreadNotifications->markAsRead();
        return redirect()->back();
    }

    public function marcarLida($id)
    {
        $user = Auth::guard('web')->user() ?? Auth::guard('barbeiro')->user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['match.stadium', 'club', 'tournament']);

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($from = $request->get('from')) {
            $query->whereDate('paid_at', '>=', $from);
        }

        if ($to = $request->get('to')) {
            $query->whereDate('paid_at', '<=', $to);
        }

        $payments = $query->orderByDesc('created_at')->paginate(25)->withQueryString();

        $totals = [
            'paid'    => Payment::where('status', 'paid')->sum('amount'),
            'pending' => Payment::where('status', 'pending')->sum('amount'),
            'failed'  => Payment::where('status', 'failed')->sum('amount'),
        ];

        return view('admin.pages.payments.index', compact('payments', 'totals'));
    }
}

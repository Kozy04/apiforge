<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiModel;
use App\Models\Click;
use App\Models\Lead;
use App\Models\Provider;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalModels'      => ApiModel::count(),
            'totalProviders'   => Provider::count(),
            'totalClicks'      => Click::count(),
            'totalLeads'       => Lead::count(),
            'totalSubscribers' => Subscriber::count(),
            'recentLeads'      => Lead::latest()->take(10)->get(),
            'recentClicks'     => Click::with(['provider', 'apiModel'])->latest()->take(20)->get(),
            'topProviders'     => Provider::withCount('clicks')->orderByDesc('clicks_count')->take(10)->get(),
            'monthClicks'      => Click::where('created_at', '>=', now()->subDays(30))->count(),
            'monthLeads'       => Lead::where('created_at', '>=', now()->subDays(30))->count(),
        ]);
    }

    public function login()
    {
        if (session('admin_authenticated')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function authenticate(Request $request)
    {
        $password = config('services.admin.password', 'admin');

        if ($request->input('password') === $password) {
            $request->session()->put('admin_authenticated', true);
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors(['password' => 'Invalid password.']);
    }

    public function logout()
    {
        session()->forget('admin_authenticated');
        return redirect()->route('admin.login');
    }

    public function markLead(string $id, Request $request)
    {
        $lead = Lead::findOrFail($id);
        $lead->update(['status' => $request->input('status', 'contacted')]);
        return back()->with('status', 'Lead updated.');
    }
}

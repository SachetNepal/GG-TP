<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Auth\ProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileWebController extends Controller
{
    public function __construct(protected ProfileService $profileService)
    {
    }

    public function index(): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $profile = $this->profileService->getProfile(Auth::user());

        return view('profile.index', ['profile' => $profile]);
    }
}

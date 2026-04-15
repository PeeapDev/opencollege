<?php

namespace App\Modules\Core\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Settings\Models\Institution;
use App\Modules\Academic\Models\Program;
use App\Modules\Communication\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FrontendController extends Controller
{
    public function index()
    {
        $institution = app()->bound('institution') ? app('institution') : null;
        if (!$institution || $institution->id == 1) {
            return redirect()->route('login');
        }

        $settings = DB::table('frontend_settings')->where('institution_id', $institution->id)->first();
        if (!$settings || !$settings->website_enabled) {
            return redirect()->route('login');
        }

        $programs = Program::where('institution_id', $institution->id)->where('active', true)->take(6)->get();
        $notices = Notice::where('institution_id', $institution->id)
            ->whereIn('audience', ['all', 'students'])
            ->where('publish_date', '<=', now())
            ->where(function ($q) { $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now()); })
            ->latest()->take(4)->get();

        $stats = [
            'students' => DB::table('students')->where('institution_id', $institution->id)->where('status', 'active')->count(),
            'programs' => Program::where('institution_id', $institution->id)->where('active', true)->count(),
            'staff' => DB::table('staff')->where('institution_id', $institution->id)->where('status', 'active')->count(),
            'departments' => DB::table('departments')->where('institution_id', $institution->id)->where('active', true)->count(),
        ];

        $admissionOpen = DB::table('admission_settings')->where('institution_id', $institution->id)->where('is_open', true)->exists();

        return view('core::frontend.home', compact('institution', 'settings', 'programs', 'notices', 'stats', 'admissionOpen'));
    }

    public function about()
    {
        $institution = app()->bound('institution') ? app('institution') : null;
        if (!$institution || $institution->id == 1) abort(404);
        $settings = DB::table('frontend_settings')->where('institution_id', $institution->id)->first();
        return view('core::frontend.about', compact('institution', 'settings'));
    }

    public function programs()
    {
        $institution = app()->bound('institution') ? app('institution') : null;
        if (!$institution || $institution->id == 1) abort(404);
        $programs = Program::where('institution_id', $institution->id)->where('active', true)->with('department.faculty')->get();
        $settings = DB::table('frontend_settings')->where('institution_id', $institution->id)->first();
        return view('core::frontend.programs', compact('institution', 'programs', 'settings'));
    }

    public function contact()
    {
        $institution = app()->bound('institution') ? app('institution') : null;
        if (!$institution || $institution->id == 1) abort(404);
        $settings = DB::table('frontend_settings')->where('institution_id', $institution->id)->first();
        return view('core::frontend.contact', compact('institution', 'settings'));
    }
}

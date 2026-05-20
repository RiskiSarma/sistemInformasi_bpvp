<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\Instructor;
use App\Models\PengajarEksternal;

class ProgramController extends Controller
{
    private function getInstructorAccess()
    {
        $user = auth()->user();

        $internal = Instructor::where('user_id', $user->id)->first();
        $external = PengajarEksternal::where('user_id', $user->id)
                    ->orWhere('email', $user->email)
                    ->first();

        return [$internal, $external];
    }

    private function applyInstructorConstraint($query, $internal, $external)
    {
        return $query->where(function($main) use ($internal, $external) {
            if ($internal) {
                $main->where(function($sub) use ($internal) {
                    $sub->where('instructor_id', $internal->id)
                        ->where('instructor_type', 'internal');
                });
            }
            if ($external) {
                $main->orWhere(function($sub) use ($external) {
                    $sub->where('pengajar_eksternal_id', $external->id);
                });
            }
        });
    }

    public function index(Request $request)
    {
        [$internal, $external] = $this->getInstructorAccess();

        if (!$internal && !$external) {
            return redirect()->route('instructor.dashboard')
                ->with('error', 'Data instruktur tidak ditemukan');
        }

        $query = Program::whereHas('programInstructors', function($q) use ($internal, $external) {
            $this->applyInstructorConstraint($q, $internal, $external);
        });

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('masterProgram', function($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                })->orWhere('angkatan', 'like', "%{$search}%");
            });
        }

        $programs = $query->with(['participants', 'masterProgram', 'paketPelatihan'])
            ->orderBy('start_date', 'desc')
            ->paginate(9);

        return view('instructor-area.programs.index', compact('programs'));
    }

    public function show(Program $program)
    {
        [$internal, $external] = $this->getInstructorAccess();

        if (!$internal && !$external) {
            abort(403, 'Data instruktur tidak ditemukan');
        }

        $hasAccess = $program->programInstructors()
            ->where(function($q) use ($internal, $external) {
                $this->applyInstructorConstraint($q, $internal, $external);
            })->exists();

        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke program ini');
        }

        $program->load([
            'participants.user',
            'participants.attendances',
            'masterProgram',
            'programInstructors.instructor',
            'programInstructors.pengajarEksternal',
            'paketPelatihan',
        ]);

        return view('instructor-area.programs.show', compact('program'));
    }
}
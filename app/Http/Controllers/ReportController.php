<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Participant;
use App\Models\Attendance;
use App\Models\Certificate;
use App\Exports\ReportsExport;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        // Ambil statistik untuk quick reports
        $stats = [
            'active_programs' => Program::where('status', 'ongoing')->count(),
            'active_participants' => Participant::where('status', 'active')->count(),
            'attendance_this_month' => Attendance::whereMonth('date', now()->month)
                                                ->whereYear('date', now()->year)
                                                ->count(),
            'certificates_issued' => Certificate::count(), // PERBAIKAN: Ambil dari table certificates
        ];

        return view('reports.index', compact('stats'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:program,participant,attendance,certificate',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $data = $this->getReportData($validated);

        return view('reports.preview', compact('data', 'validated'));
    }

    public function export($type, Request $request)
    {
        $reportType = $request->input('report_type', 'program');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $data = $this->getReportData([
            'report_type' => $reportType,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf', compact('data', 'reportType', 'dateFrom', 'dateTo'));
            return $pdf->download('laporan-' . $reportType . '-' . now()->format('Y-m-d') . '.pdf');
        } elseif ($type === 'excel') {
            return Excel::download(
                new ReportsExport($data, $reportType),
                'laporan-' . $reportType . '-' . now()->format('Y-m-d') . '.xlsx'
            );
        }

        return redirect()->back()->with('error', 'Tipe export tidak valid');
    }

    // Quick report methods
    public function activePrograms()
    {
        $validated = [
            'report_type' => 'program',
            'date_from' => null,
            'date_to' => null,
        ];
        
        $data = Program::where('status', 'ongoing')
                      ->with('participants')
                      ->get();

        return view('reports.preview', compact('data', 'validated'));
    }

    public function activeParticipants()
    {
        $validated = [
            'report_type' => 'participant',
            'date_from' => null,
            'date_to' => null,
        ];
        
        $data = Participant::where('status', 'active')
                          ->with('program')
                          ->get();

        return view('reports.preview', compact('data', 'validated'));
    }

    public function attendanceThisMonth()
    {
        $validated = [
            'report_type' => 'attendance',
            'date_from' => now()->startOfMonth()->format('Y-m-d'),
            'date_to' => now()->endOfMonth()->format('Y-m-d'),
        ];
        
        $data = Attendance::whereMonth('date', now()->month)
                         ->whereYear('date', now()->year)
                         ->with(['program', 'participant'])
                         ->get();

        return view('reports.preview', compact('data', 'validated'));
    }

    public function certificatesIssued()
    {
        $validated = [
            'report_type' => 'certificate',
            'date_from' => null,
            'date_to' => null,
        ];
        
        // PERBAIKAN: Ambil dari table certificates, bukan participants
        $data = Certificate::with(['participant.program.masterProgram'])
                          ->get()
                          ->map(function($cert) {
                              return $cert->participant;
                          });

        return view('reports.preview', compact('data', 'validated'));
    }

    private function getReportData($params)
    {
        switch ($params['report_type']) {
            case 'program':
                $query = Program::with(['masterProgram', 'participants']);
                
                // Filter by date if provided
                if (isset($params['date_from']) && $params['date_from']) {
                    $query->whereDate('created_at', '>=', $params['date_from']);
                }
                if (isset($params['date_to']) && $params['date_to']) {
                    $query->whereDate('created_at', '<=', $params['date_to']);
                }
                break;

            case 'participant':
                $query = Participant::with(['program.masterProgram']);
                
                if (isset($params['date_from']) && $params['date_from']) {
                    $query->whereDate('created_at', '>=', $params['date_from']);
                }
                if (isset($params['date_to']) && $params['date_to']) {
                    $query->whereDate('created_at', '<=', $params['date_to']);
                }
                break;

            case 'attendance':
                $query = Attendance::with(['program.masterProgram', 'participant']);
                
                if (isset($params['date_from']) && $params['date_from']) {
                    $query->whereDate('date', '>=', $params['date_from']);
                }
                if (isset($params['date_to']) && $params['date_to']) {
                    $query->whereDate('date', '<=', $params['date_to']);
                }
                break;

            case 'certificate':
                // PERBAIKAN: Ambil dari table certificates
                $certificateQuery = Certificate::with(['participant.program.masterProgram']);
                
                if (isset($params['date_from']) && $params['date_from']) {
                    $certificateQuery->whereDate('created_at', '>=', $params['date_from']);
                }
                if (isset($params['date_to']) && $params['date_to']) {
                    $certificateQuery->whereDate('created_at', '<=', $params['date_to']);
                }
                
                // Map ke format participant untuk compatibility dengan view
                return $certificateQuery->orderBy('created_at', 'desc')
                                       ->get()
                                       ->map(function($cert) {
                                           return $cert->participant;
                                       });

            default:
                $query = Program::with(['masterProgram', 'participants']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}
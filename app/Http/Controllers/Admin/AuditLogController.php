<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('utilisateur');

        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }
        if ($modele = $request->input('modele')) {
            $query->where('modele', $modele);
        }
        if ($userId = $request->input('utilisateur_id')) {
            $query->where('utilisateur_id', $userId);
        }
        if ($dateDebut = $request->input('date_debut')) {
            $query->whereDate('created_at', '>=', $dateDebut);
        }
        if ($dateFin = $request->input('date_fin')) {
            $query->whereDate('created_at', '<=', $dateFin);
        }

        $logs = $query->latest()->paginate(50);
        $actions = AuditLog::distinct()->pluck('action');
        $modeles = AuditLog::distinct()->pluck('modele');

        return view('admin.audit-logs.index', compact('logs', 'actions', 'modeles'));
    }
}

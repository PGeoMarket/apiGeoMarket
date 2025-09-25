<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\ReasonComplaint;
use App\Models\Publication;
use App\Models\User;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::included()->filter()->sort()->getOrPaginate();
        return response()->json($reports);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'reportable_type' => 'required|in:publication,user',
            'reportable_id'   => 'required|integer',
            'reason_id'       => 'required|exists:reason_complaints,id',
            'descripcion_adicional' => 'nullable|string|max:1000',
            'user_id'         => 'required|exists:users,id',
        ]);

        $userId = $data['user_id'];

        $reason = ReasonComplaint::find($data['reason_id']);
        if (!$reason->appliesTo($data['reportable_type'])) {
            return response()->json([
                'message' => 'La razón seleccionada no aplica para este tipo de reporte.'
            ], 400);
        }

        if ($data['reportable_type'] === 'user' && $data['reportable_id'] == $userId) {
            return response()->json(['message' => 'No puedes reportarte a ti mismo.'], 400);
        }

        if ($data['reportable_type'] === 'publication') {
            $publication = Publication::find($data['reportable_id']);
            if ($publication && $publication->seller->user_id == $userId) {
                return response()->json(['message' => 'No puedes reportar tu propia publicación.'], 400);
            }
        }

        $existingReport = Report::where('user_id', $userId)
            ->where('reportable_type', $data['reportable_type'])
            ->where('reportable_id', $data['reportable_id'])
            ->where('reason_id', $data['reason_id'])
            ->first();

        if ($existingReport) {
            return response()->json(['message' => 'Ya has reportado este elemento por la misma razón.'], 400);
        }

        $data['user_id'] = $userId;
        $report = Report::create($data);

        return response()->json([
            'message' => 'Reporte creado correctamente.',
            'report'  => $report->load(['reporter', 'reason', 'reportable']),
        ], 201);
    }

    public function show(Report $report)
    {
        $report->load(['reason', 'reportable']);
        return response()->json($report);
    }

    public function update(Request $request, Report $report)
    {
        $data = $request->validate([
            'estado' => 'required|boolean'
        ]);

        $report->update($data);

        $message = $data['estado'] ? 'Reporte marcado como resuelto.' : 'Reporte marcado como pendiente.';

        return response()->json([
            'message' => $message,
            'report' => $report,
        ], 200);
    }

    public function destroy($id)
    {
        $report = Report::find($id);

        if (!$report) {
            return response()->json([
                'error' => 'Reporte no encontrado.'
            ], 404);
        }

        if ($report->delete()) {
            return response()->json([
                'message' => 'Reporte eliminado correctamente.'
            ], 200);
        }

        return response()->json([
            'error' => 'No se pudo eliminar el reporte.'
        ], 400);
    }

   public function reportPublication(Request $request, Publication $publication)
    {
        $data = $request->validate([
            'reason_id' => 'required|exists:reason_complaints,id',
            'descripcion_adicional' => 'nullable|string|max:1000',
            'user_id'   => 'nullable|exists:users,id',
        ]);

        $userId = $data['user_id'] ?? 1;

        $reason = ReasonComplaint::find($data['reason_id']);
        if (!$reason->appliesTo('publication')) {
            return response()->json(['message' => 'La razón seleccionada no aplica para publicaciones.'], 400);
        }

        if ($publication->seller->user_id == $userId) {
            return response()->json(['message' => 'No puedes reportar tu propia publicación.'], 400);
        }

        $report = Report::create([
            'user_id'            => $userId,
            'reportable_type'    => 'publication',
            'reportable_id'      => $publication->id,
            'reason_id'          => $data['reason_id'],
            'descripcion_adicional' => $data['descripcion_adicional'] ?? null
        ]);

        return response()->json([
            'message' => 'Publicación reportada correctamente.',
            'report'  => $report->load(['reporter', 'reason', 'reportable']),
        ], 201);
    }

   public function reportUser(Request $request, User $user)
    {
        $data = $request->validate([
            'reason_id' => 'required|exists:reason_complaints,id',
            'descripcion_adicional' => 'nullable|string|max:1000',
            'user_id'   => 'nullable|exists:users,id',
        ]);

        $userId = $data['user_id'] ?? 1;

        $reason = ReasonComplaint::find($data['reason_id']);
        if (!$reason->appliesTo('user')) {
            return response()->json(['message' => 'La razón seleccionada no aplica para usuarios.'], 400);
        }

        if ($user->id == $userId) {
            return response()->json(['message' => 'No puedes reportarte a ti mismo.'], 400);
        }

        $report = Report::create([
            'user_id'            => $userId,
            'reportable_type'    => 'user',
            'reportable_id'      => $user->id,
            'reason_id'          => $data['reason_id'],
            'descripcion_adicional' => $data['descripcion_adicional'] ?? null
        ]);

        return response()->json([
            'message' => 'Usuario reportado correctamente.',
            'report'  => $report->load(['reporter', 'reason', 'reportable']),
        ], 201);
    }
}
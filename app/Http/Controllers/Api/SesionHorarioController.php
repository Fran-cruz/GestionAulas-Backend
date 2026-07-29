<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\SesionHorario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SesionHorarioController extends Controller
{
    /**
     * GET /api/sesiones-horario
     * Lista todas las sesiones.
     */
    public function index(): JsonResponse
    {
        return response()->json(SesionHorario::all());
    }

    /**
     * POST /api/sesiones-horario
     * Crea una sesión nueva.
     */
    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'id_asignacion' => ['required', 'integer', 'exists:asignaciones,id'],
            'dia' => ['required', Rule::in(['LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO'])],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'generado_automaticamente' => ['nullable', 'boolean'],
        ]);

        $datos['generado_automaticamente'] = $datos['generado_automaticamente'] ?? false;

        $sesion = SesionHorario::create($datos);

        return response()->json([
            'message' => 'Sesión de horario creada correctamente.',
            'sesion' => $sesion,
        ], 201);
    }

    /**
     * GET /api/sesiones-horario/{sesionHorario}
     * Muestra una sola sesión.
     */
    public function show(SesionHorario $sesionHorario): JsonResponse
    {
        return response()->json($sesionHorario);
    }

    /**
     * PUT/PATCH /api/sesiones-horario/{sesionHorario}
     * Actualiza una sesión. Acepta actualizaciones parciales.
     */
    public function update(Request $request, SesionHorario $sesionHorario): JsonResponse
    {
        $datos = $request->validate([
            'id_asignacion' => ['sometimes', 'required', 'integer', 'exists:asignaciones,id'],
            'dia' => ['sometimes', 'required', Rule::in(['LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO'])],
            'hora_inicio' => ['sometimes', 'required', 'date_format:H:i'],
            'hora_fin' => ['sometimes', 'required', 'date_format:H:i', 'after:hora_inicio'],
            'generado_automaticamente' => ['sometimes', 'boolean'],
        ]);

        $sesionHorario->update($datos);

        return response()->json([
            'message' => 'Sesión de horario actualizada correctamente.',
            'sesion' => $sesionHorario,
        ]);
    }

    /**
     * DELETE /api/sesiones-horario/{sesionHorario}
     * Elimina una sesión.
     */
    public function destroy(SesionHorario $sesionHorario): JsonResponse
    {
        $sesionHorario->delete();

        return response()->json([
            'message' => 'Sesión de horario eliminada correctamente.',
        ]);
    }
}

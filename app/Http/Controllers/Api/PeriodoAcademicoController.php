<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\PeriodoAcademico;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PeriodoAcademicoController extends Controller
{
    /**
     * GET /api/periodos-academicos
     * Lista todos los periodos.
     */
    public function index(): JsonResponse
    {
        return response()->json(PeriodoAcademico::all());
    }

    /**
     * POST /api/periodos-academicos
     * Crea un periodo nuevo.
     */
    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100', 'unique:periodos_academicos,nombre'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'estado' => ['nullable', Rule::in(['ACTIVO', 'CERRADO'])],
            'id_usuario_creador' => ['required', 'integer', 'exists:usuarios,id'],
        ]);

        $datos['estado'] = $datos['estado'] ?? 'ACTIVO';

        $periodo = PeriodoAcademico::create($datos);

        return response()->json([
            'message' => 'Periodo académico creado correctamente.',
            'periodo' => $periodo,
        ], 201);
    }

    /**
     * GET /api/periodos-academicos/{periodo}
     * Muestra un solo periodo.
     */
    public function show(PeriodoAcademico $periodo): JsonResponse
    {
        return response()->json($periodo);
    }

    /**
     * PUT/PATCH /api/periodos-academicos/{periodo}
     * Actualiza un periodo. Acepta actualizaciones parciales.
     */
    public function update(Request $request, PeriodoAcademico $periodo): JsonResponse
    {
        $datos = $request->validate([
            'nombre' => [
                'sometimes', 'required', 'string', 'max:100',
                Rule::unique('periodos_academicos', 'nombre')->ignore($periodo->id),
            ],
            'fecha_inicio' => ['sometimes', 'required', 'date'],
            'fecha_fin' => ['sometimes', 'required', 'date', 'after_or_equal:fecha_inicio'],
            'estado' => ['sometimes', Rule::in(['ACTIVO', 'CERRADO'])],
            'id_usuario_creador' => ['sometimes', 'required', 'integer', 'exists:usuarios,id'],
        ]);

        $periodo->update($datos);

        return response()->json([
            'message' => 'Periodo académico actualizado correctamente.',
            'periodo' => $periodo,
        ]);
    }

    /**
     * DELETE /api/periodos-academicos/{periodo}
     * Elimina un periodo.
     */
    public function destroy(PeriodoAcademico $periodo): JsonResponse
    {
        $periodo->delete();

        return response()->json([
            'message' => 'Periodo académico eliminado correctamente.',
        ]);
    }
}
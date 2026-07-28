<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Docente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DocenteController extends Controller
{
    /**
     * GET /api/docentes
     * Lista todos los docentes.
     */
    public function index(): JsonResponse
    {
        return response()->json(Docente::all());
    }

    /**
     * POST /api/docentes
     * Crea un docente nuevo.
     */
    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'nombre_completo' => ['required', 'string', 'max:150'],
            'correo_institucional' => ['required', 'string', 'email', 'max:150', 'unique:docentes,correo_institucional'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'departamento' => ['required', 'string', 'max:100'],
            'especialidad' => ['nullable', 'string', 'max:150'],
            'estado' => ['nullable', Rule::in(['ACTIVO', 'LICENCIA', 'INACTIVO'])],
        ]);

        $datos['estado'] = $datos['estado'] ?? 'ACTIVO';

        $docente = Docente::create($datos);

        return response()->json([
            'message' => 'Docente creado correctamente.',
            'docente' => $docente,
        ], 201);
    }

    /**
     * GET /api/docentes/{docente}
     * Muestra un solo docente.
     */
    public function show(Docente $docente): JsonResponse
    {
        return response()->json($docente);
    }

    /**
     * PUT/PATCH /api/docentes/{docente}
     * Actualiza un docente. Acepta actualizaciones parciales.
     */
    public function update(Request $request, Docente $docente): JsonResponse
    {
        $datos = $request->validate([
            'nombre_completo' => ['sometimes', 'required', 'string', 'max:150'],
            'correo_institucional' => [
                'sometimes', 'required', 'string', 'email', 'max:150',
                Rule::unique('docentes', 'correo_institucional')->ignore($docente->id),
            ],
            'telefono' => ['nullable', 'string', 'max:20'],
            'departamento' => ['sometimes', 'required', 'string', 'max:100'],
            'especialidad' => ['nullable', 'string', 'max:150'],
            'estado' => ['sometimes', Rule::in(['ACTIVO', 'LICENCIA', 'INACTIVO'])],
        ]);

        $docente->update($datos);

        return response()->json([
            'message' => 'Docente actualizado correctamente.',
            'docente' => $docente,
        ]);
    }

    /**
     * DELETE /api/docentes/{docente}
     * Elimina un docente.
     */
    public function destroy(Docente $docente): JsonResponse
    {
        $docente->delete();

        return response()->json([
            'message' => 'Docente eliminado correctamente.',
        ]);
    }
}
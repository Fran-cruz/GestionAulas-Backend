<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asignacion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AsignacionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Asignacion::with(['seccion', 'periodo', 'aula', 'docente'])->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'id_seccion' => [
                'required', 'integer', 'exists:secciones,id',
                Rule::unique('asignaciones')->where(
                    fn ($query) => $query->where('id_periodo', $request->input('id_periodo'))
                ),
            ],
            'id_periodo' => ['required', 'integer', 'exists:periodos_academicos,id'],
            'id_aula' => ['nullable', 'integer', 'exists:aulas,id'],
            'id_docente' => ['nullable', 'integer', 'exists:docentes,id'],
            'estudiantes_matriculados' => ['nullable', 'integer', 'min:0'],
            'sobrecargo_confirmado' => ['nullable', 'boolean'],
            'estado' => ['nullable', Rule::in(['ACTIVA', 'ASIGNADA'])],
        ]);

        $datos['estudiantes_matriculados'] = $datos['estudiantes_matriculados'] ?? 0;
        $datos['sobrecargo_confirmado'] = $datos['sobrecargo_confirmado'] ?? false;
        $datos['estado'] = $datos['estado'] ?? 'ACTIVA';

        $asignacion = Asignacion::create($datos);

        return response()->json([
            'message' => 'Asignación creada correctamente.',
            'asignacion' => $asignacion->load(['seccion', 'periodo', 'aula', 'docente']),
        ], 201);
    }

    public function show(Asignacion $asignacion): JsonResponse
    {
        return response()->json(
            $asignacion->load(['seccion', 'periodo', 'aula', 'docente'])
        );
    }

    public function update(Request $request, Asignacion $asignacion): JsonResponse
    {
        $datos = $request->validate([
            'id_seccion' => [
                'sometimes', 'required', 'integer', 'exists:secciones,id',
                Rule::unique('asignaciones')->ignore($asignacion->id)->where(
                    fn ($query) => $query->where(
                        'id_periodo',
                        $request->input('id_periodo', $asignacion->id_periodo)
                    )
                ),
            ],
            'id_periodo' => ['sometimes', 'required', 'integer', 'exists:periodos_academicos,id'],
            'id_aula' => ['nullable', 'integer', 'exists:aulas,id'],
            'id_docente' => ['nullable', 'integer', 'exists:docentes,id'],
            'estudiantes_matriculados' => ['sometimes', 'required', 'integer', 'min:0'],
            'sobrecargo_confirmado' => ['sometimes', 'boolean'],
            'estado' => ['sometimes', Rule::in(['ACTIVA', 'ASIGNADA'])],
        ]);

        $asignacion->update($datos);

        return response()->json([
            'message' => 'Asignación actualizada correctamente.',
            'asignacion' => $asignacion->load(['seccion', 'periodo', 'aula', 'docente']),
        ]);
    }

    public function destroy(Asignacion $asignacion): JsonResponse
    {
        $asignacion->delete();

        return response()->json([
            'message' => 'Asignación eliminada correctamente.',
        ]);
    }
}
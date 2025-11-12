<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DocumentoController extends Controller
{
    /**
     * Download de documento
     */
    public function download(Documento $documento)
    {
        // Verifica permissão
        if (!Auth::user()->can('ver_documentos')) {
            abort(403, 'Você não tem permissão para visualizar este documento.');
        }

        // Verifica se o arquivo existe
        if (!Storage::disk('public')->exists($documento->path_arquivo)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return Storage::disk('public')->download($documento->path_arquivo, $documento->titulo_documento);
    }

    /**
     * Visualizar documento (para PDFs e imagens)
     */
    public function visualizar(Documento $documento)
    {
        // Verifica permissão
        if (!Auth::user()->can('ver_documentos')) {
            abort(403, 'Você não tem permissão para visualizar este documento.');
        }

        // Verifica se o arquivo existe
        if (!Storage::disk('public')->exists($documento->path_arquivo)) {
            abort(404, 'Arquivo não encontrado.');
        }

        $path = Storage::disk('public')->path($documento->path_arquivo);
        $mimeType = Storage::disk('public')->mimeType($documento->path_arquivo);

        return response()->file($path, [
            'Content-Type' => $mimeType,
        ]);
    }
}


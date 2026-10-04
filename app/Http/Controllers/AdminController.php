<?php

namespace App\Http\Controllers;

use App\Mail\RespuestaReclamacion;
use App\Models\Reclamacion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class AdminController extends Controller
{
    // Bandeja de reclamaciones con filtros
    public function index(Request $request)
    {
        $estado = $request->query('estado');
        $buscar = trim((string) $request->query('buscar'));

        $reclamaciones = Reclamacion::query()
            ->when($estado === 'vencidas', fn ($q) => $q->vencidas())
            ->when(in_array($estado, [Reclamacion::PENDIENTE, Reclamacion::ATENDIDO], true), fn ($q) => $q->where('estado', $estado))
            ->when($buscar !== '', function ($q) use ($buscar) {
                $q->where(function ($q) use ($buscar) {
                    $q->where('numero', 'like', "%{$buscar}%")
                      ->orWhere('nombres_apellidos', 'like', "%{$buscar}%")
                      ->orWhere('numero_documento', 'like', "%{$buscar}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $resumen = [
            'total'      => Reclamacion::count(),
            'pendientes' => Reclamacion::pendientes()->count(),
            'vencidas'   => Reclamacion::vencidas()->count(),
            'atendidas'  => Reclamacion::where('estado', Reclamacion::ATENDIDO)->count(),
        ];

        return view('admin.dashboard', compact('reclamaciones', 'resumen', 'estado', 'buscar'));
    }

    // Detalle de una reclamación
    public function show(Reclamacion $reclamacion)
    {
        return view('admin.show', compact('reclamacion'));
    }

    // Registrar la respuesta del proveedor y notificar al consumidor
    public function responder(Request $request, Reclamacion $reclamacion)
    {
        abort_unless($reclamacion->estaPendiente(), 422, 'La reclamación ya fue atendida.');

        $datos = $request->validate([
            'respuesta' => 'required|string|min:10|max:5000',
        ]);

        $reclamacion->responder($datos['respuesta'], $request->user());

        Mail::to($reclamacion->correo)->send(new RespuestaReclamacion($reclamacion));

        return redirect()
            ->route('admin.show', $reclamacion)
            ->with('success', 'Respuesta registrada y enviada al correo del consumidor.');
    }

    // Ver la evidencia PDF adjunta (almacenada en disco privado)
    public function evidencia(Reclamacion $reclamacion)
    {
        $path = $reclamacion->evidencia_path;

        abort_unless($path && Storage::exists($path), 404, 'Archivo no encontrado.');

        return response()->file(Storage::path($path), ['Content-Type' => 'application/pdf']);
    }

    // Descargar la hoja de reclamación en PDF (con la evidencia al final)
    public function reporte(Reclamacion $reclamacion)
    {
        $archivo = 'Hoja_Reclamacion_'.$reclamacion->numero.'.pdf';
        $reporte = Pdf::loadView('admin.reporte-pdf', compact('reclamacion'))
            ->setPaper('a4')
            ->output();

        $path = $reclamacion->evidencia_path;
        $contenido = ($path && Storage::exists($path))
            ? $this->fusionarConEvidencia($reporte, Storage::path($path), $reclamacion->numero)
            : $reporte;

        return response($contenido, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$archivo.'"',
        ]);
    }

    private function fusionarConEvidencia(string $reporte, string $evidencia, string $numero): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'rpt_');

        try {
            file_put_contents($tmp, $reporte);

            $pdf = new Fpdi();
            $pdf->SetAutoPageBreak(false);
            $this->importarPaginas($pdf, $tmp);

            // Separador antes de la evidencia
            $pdf->AddPage('P', [210, 297]);
            $pdf->SetFont('Helvetica', 'B', 13);
            $pdf->SetY(130);
            $pdf->Cell(0, 10, 'EVIDENCIA ADJUNTA POR EL CONSUMIDOR', 0, 1, 'C');
            $pdf->SetFont('Helvetica', '', 10);
            $pdf->Cell(0, 8, 'Hoja de reclamacion N. '.$numero, 0, 1, 'C');

            $this->importarPaginas($pdf, $evidencia);

            return $pdf->Output('S');
        } catch (\Throwable $e) {
            report($e);

            return $reporte; // PDF de evidencia incompatible: se entrega solo el reporte
        } finally {
            @unlink($tmp);
        }
    }

    private function importarPaginas(Fpdi $pdf, string $archivo): void
    {
        $total = $pdf->setSourceFile($archivo);

        for ($p = 1; $p <= $total; $p++) {
            $tpl = $pdf->importPage($p);
            $size = $pdf->getTemplateSize($tpl);
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($tpl);
        }
    }
}

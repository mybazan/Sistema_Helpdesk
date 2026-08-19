<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;
use App\Entity\Equipo;
use App\Entity\PlanillaEquipo;
use App\Entity\UsuarioEquipo;
use App\Repository\UsuarioEquipoRepository;

class GenerateDocument
{
    private $twig;
    private $usuarioEquipoRepository;
    private $projectDir;

    public function __construct(Environment $twig, UsuarioEquipoRepository $usuarioEquipoRepository, string $projectDir)
    {
        $this->twig = $twig;
        $this->usuarioEquipoRepository = $usuarioEquipoRepository;
        $this->projectDir = $projectDir;
    }

    public function generateDocumentPDF(string $html): string
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);
        $pdfOptions->setIsHtml5ParserEnabled(true);

        $dompdf = new Dompdf($pdfOptions);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    public function generateEquipos(array $equipos, ?string $projectDir = null, array $filters = []): string
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        $html = $this->twig->render('equipo/print_equipos.html.twig', [
            'filtros' => $filters,
            'equipos' => $equipos,
            'logo' => $this->loadLogoBase64($projectDir),
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    public function generateEquipoInfo(Equipo $equipo, $projectDirOrBlocks = null, array $requestedBlocks = []): string
    {
        // Compatibilidad: algunos llamados pasan solo ($equipo, $requestedBlocks)
        if (is_array($projectDirOrBlocks)) {
            $requestedBlocks = $projectDirOrBlocks;
            $projectDirOrBlocks = null;
        }

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        $planillaEquipo = $equipo->getPlanillaEquipo();
        $historiales = $equipo->getEquipoHistorial();

        $usuariosEquipo = [];
        if ($requestedBlocks['show_historial'] ?? false) {
            $usuariosEquipo = $this->usuarioEquipoRepository->findAllUsuariosByEquipoOrdered($equipo);
        }

        usort($usuariosEquipo, function (UsuarioEquipo $a, UsuarioEquipo $b) {
            return $b->getIsActual() <=> $a->getIsActual();
        });

        $tipoNombre = $equipo->getTipo() ? $equipo->getTipo()->getNombre() : '';
        $autoBlocks = $this->determinarBloquesPorTipo($tipoNombre, $planillaEquipo);
        $bloques = $this->mergeRequestedBlocks($autoBlocks, $requestedBlocks);

        $html = $this->twig->render('equipo/print_equipo_detalle.html.twig', [
            'equipo' => $equipo,
            'planilla' => $planillaEquipo,
            'historiales' => $historiales,
            'usuariosEquipo' => $usuariosEquipo,
            'logo' => $this->loadLogoBase64(is_string($projectDirOrBlocks) ? $projectDirOrBlocks : null),
            'fechaReporte' => new \DateTime(),
            'bloques' => $bloques,
            'condiciones' => [
                1 => 'Activo',
                2 => 'Prestado',
                3 => 'Fuera de Servicio',
            ],
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    private function loadLogoBase64(?string $projectDir = null): string
    {
        $baseDir = rtrim($projectDir ?: $this->projectDir, '/\\');
        $candidates = [
            $baseDir . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'base' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'logo.png',
            $baseDir . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'logo_secretaria.png',
        ];

        foreach ($candidates as $logoPath) {
            if (is_file($logoPath) && is_readable($logoPath)) {
                return base64_encode(file_get_contents($logoPath));
            }
        }

        return '';
    }

    private function mergeRequestedBlocks(array $autoBlocks, array $requestedBlocks): array
    {
        if (empty($requestedBlocks)) {
            return $autoBlocks;
        }

        $asBool = static function ($value): bool {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN) || $value === 'on' || $value === '1';
        };

        return [
            'show_datos_basicos' => $requestedBlocks['show_datos_basicos'] ?? $autoBlocks['show_datos_basicos'],
            'show_especificaciones' => $asBool($requestedBlocks['show_especificaciones'] ?? false)
                ? ($autoBlocks['show_especificaciones'] ?: ['procesador', 'memoria_ram', 'fuente', 'monitor', 'ups', 'red', 'observacion'])
                : [],
            'show_almacenamiento' => $asBool($requestedBlocks['show_almacenamiento'] ?? false),
            'show_sistemas_operativos' => $asBool($requestedBlocks['show_sistemas_operativos'] ?? false),
            'show_accesos' => $asBool($requestedBlocks['show_accesos'] ?? false),
            'show_historial' => $asBool($requestedBlocks['show_historial'] ?? false),
        ];
    }

    private function determinarBloquesPorTipo(string $tipo, ?PlanillaEquipo $planilla): array
    {
        $bloques = [
            'show_datos_basicos' => true,
            'show_historial' => true,
            'show_almacenamiento' => false,
            'show_sistemas_operativos' => false,
            'show_accesos' => false,
            'show_especificaciones' => [],
        ];

        if (!$planilla) {
            return $bloques;
        }

        $bloques['show_almacenamiento'] = $planilla->getAlmacenamientos()->count() > 0;
        $bloques['show_sistemas_operativos'] = $planilla->getSistemasOperativos()->count() > 0;
        $bloques['show_accesos'] = $planilla->getAccesos()->count() > 0;

        switch ($tipo) {
            case 'Notebook':
            case 'Computadora de Escritorio':
            case 'Servidor':
                $bloques['show_especificaciones'] = [
                    'procesador',
                    'memoria_ram',
                    'fuente',
                    'monitor',
                    'ups',
                    'red',
                    'observacion',
                ];
                if ($tipo === 'Servidor') {
                    $bloques['show_especificaciones'][] = 'puertos';
                }
                break;

            case 'Impresora':
            case 'Fotocopiadora':
                $bloques['show_especificaciones'] = [
                    'toner',
                    'drum',
                    'ups',
                    'red',
                    'observacion',
                ];
                break;

            case 'DVR':
                $bloques['show_especificaciones'] = [
                    'canales',
                    'canales_libres',
                    'resolucion_grabacion',
                    'tiempo_grabacion',
                    'ups',
                    'red',
                    'observacion',
                ];
                break;

            case 'Cámara':
                $bloques['show_especificaciones'] = [
                    'megapixeles',
                    'ups',
                    'red',
                    'observacion',
                ];
                break;

            case 'Reloj Biométrico':
                $bloques['show_especificaciones'] = [
                    'personas_registradas',
                    'capacidad_registros',
                    'fuente',
                    'ups',
                    'red',
                    'observacion',
                ];
                break;

            case 'Interconexión':
            case 'Dispositivo de Interconexión':
                $bloques['show_especificaciones'] = [
                    'puertos',
                    'velocidad',
                    'ups',
                    'red',
                    'observacion',
                ];
                break;
        }

        return $bloques;
    }
}

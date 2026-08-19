<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GenerateExcelDocument
{
    /*
     * Genera un archivo Excel con la información de los equipos
     * @param array $equipos Array de equipos
     * @param array $filtros Filtros
     * @return string Ruta del archivo
     */
    public function generateEquiposExcel(array $equipos, array $filtros = []): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->freezePane('A2');
        $sheet->setTitle('Listado de Equipos');
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Estilos
        $styleEncabezadoArray = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
                'size' => 16,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '92D050'],
            ],
        ];

        $styleContenidoArray = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $row = 1;
        $col = 1;

        // Encabezados
        $headers = [
            'ID',
            'TIPO DE EQUIPO',
            'HOSTNAME',
            'DIRECCIÓN IP',
            'DIRECCIÓN MAC',
            'OBSERVACIÓN (EQUIPO)',
            'ESTADO',
            'PROCESADOR',
            'MEMORIA RAM',
            'ALMACENAMIENTO',
            'FUENTE',
            'MONITOR',
            'RED',
            'UPS',
            'OBSERVACIÓN (PLANILLA)',
            'SISTEMA OPERATIVO',
            'VERSIÓN',
            'USUARIO',
            'CONTRASEÑA',
            'PERSONAL',
            'UBICACIÓN',
        ];

        foreach (array_values($headers) as $index => $header) {
            $columnIndex = $col + $index;
            $cellCoordinate = $sheet->getCellByColumnAndRow($columnIndex, $row)->getCoordinate();
            $sheet->setCellValueByColumnAndRow($columnIndex, $row, $header);
            $sheet->getStyle($cellCoordinate)->applyFromArray($styleEncabezadoArray);
        }

        // Ajuste de ancho de columnas
        foreach (range(1, count($headers)) as $colIndex) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
        }

        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));

        $row++;

        // Cuerpo de la tabla
        foreach ($equipos as $equipo) {
            $column = 1;

            // Datos del equipo: ID, tipo, hostname, IP, MAC y observación
            $sheet->setCellValueByColumnAndRow($column++, $row, $equipo->getId());
            $sheet->setCellValueByColumnAndRow($column++, $row, $equipo->getTipo()->getNombre());
            $sheet->setCellValueByColumnAndRow($column++, $row, $equipo->getNombre());
            $sheet->setCellValueByColumnAndRow($column++, $row, $equipo->getIp());
            $sheet->setCellValueByColumnAndRow($column++, $row, $equipo->getMac());
            $sheet->setCellValueByColumnAndRow($column++, $row, $equipo->getObservacion());

            // Estado del equipo
            $condiciones = [
                1 => 'Activo',
                2 => 'Prestado',
                3 => 'Fuera de Servicio',
                4 => 'Sin Condición'
            ];

            $condicion = $condiciones[$equipo->getCondicion()] ?? 'Sin Condición';
            $sheet->setCellValueByColumnAndRow($column++, $row, $condicion);

            // Datos de la planilla
            if ($equipo->getPlanillaEquipo()) {
                // Datos de procesador y memoria RAM
                $sheet->setCellValueByColumnAndRow($column++, $row, $equipo->getPlanillaEquipo()->getProcesador());
                $sheet->setCellValueByColumnAndRow($column++, $row, $equipo->getPlanillaEquipo()->getMemoriaRam());

                // Datos de almacenamientos
                $almacenamiento = [];
                $roles = [
                    1 => 'Principal',
                    2 => 'Secundario',
                    3 => 'Terciario',
                    4 => 'Backup',
                    5 => 'Sin Uso',
                ];

                foreach ($equipo->getPlanillaEquipo()->getAlmacenamientos() as $alm) {
                    $rol = $alm->getRol();
                    $tipo = $alm->getTipo() ?? 'Tipo no especificado';
                    $capacidad = $alm->getCapacidad() ?? '0';

                    // CONTEMPLA LOS NULOS Y VALORES INVÁLIDOS
                    if ($rol === null) {
                        $rolTexto = 'Sin rol asignado';
                    } elseif (!is_numeric($rol)) {
                        $rolTexto = 'Rol inválido (no numérico)';
                    } elseif (!isset($roles[$rol])) {
                        $rolTexto = 'Rol desconocido (ID: ' . $rol . ')';
                    } else {
                        $rolTexto = $roles[$rol];
                    }

                    $almacenamiento[] = $tipo . ': ' . $capacidad . ' GB (' . $rolTexto . ')';
                }

                $sheet->setCellValueByColumnAndRow($column, $row, implode("\n", $almacenamiento));
                $sheet->getStyleByColumnAndRow($column, $row)->getAlignment()->setWrapText(true);
                $column++;

                // Datos de fuente, monitor, red, UPS y observación de planilla
                $sheet->setCellValueByColumnAndRow($column++, $row, $equipo->getPlanillaEquipo()->getFuente());
                $sheet->setCellValueByColumnAndRow($column++, $row, $equipo->getPlanillaEquipo()->getMonitor());
                $sheet->setCellValueByColumnAndRow($column++, $row, $equipo->getPlanillaEquipo()->getRed());
                $sheet->setCellValueByColumnAndRow($column++, $row, $equipo->getPlanillaEquipo()->getUps());
                $sheet->setCellValueByColumnAndRow($column++, $row, $equipo->getPlanillaEquipo()->getObservacion());

                // Datos de los sistemas operativos y credenciales de acceso
                $nombreSO = [];
                $versionSO = [];
                $usuarioAcceso = [];
                $contraseniaAcceso = [];

                if (count($equipo->getPlanillaEquipo()->getSistemasOperativos()) > 0) {
                    foreach ($equipo->getPlanillaEquipo()->getSistemasOperativos() as $so) {
                        $nombreSO[] = $so->getNombre();
                        $versionSO[] = $so->getVersion();

                        foreach ($so->getAccesos() as $acceso) {
                            $usuarioAcceso[] = ($acceso->getSistemaOperativo() ? '(' . $acceso->getSistemaOperativo()->getNombre() . ' - ' . $acceso->getAplicacion() . ') ' : '') . $acceso->getUsuario();
                            $contraseniaAcceso[] = ($acceso->getSistemaOperativo() ? '(' . $acceso->getSistemaOperativo()->getNombre() . ' - ' . $acceso->getAplicacion() . ' - ' . $acceso->getUsuario() . ') ' : '') . $acceso->getClave();
                        }
                    }
                } else {
                    foreach ($equipo->getPlanillaEquipo()->getAccesos() as $acceso) {
                        $usuarioAcceso[] = '(' . $acceso->getAplicacion() . ') ' . $acceso->getUsuario();
                        $contraseniaAcceso[] = '(' . $acceso->getAplicacion() . ' - ' . $acceso->getUsuario() . ') ' . $acceso->getClave();
                    }
                }

                $sheet->setCellValueByColumnAndRow($column, $row, implode("\n", $nombreSO));
                $sheet->getStyleByColumnAndRow($column, $row)->getAlignment()->setWrapText(true);

                $column++;

                $sheet->setCellValueByColumnAndRow($column, $row, implode("\n", $versionSO));
                $sheet->getStyleByColumnAndRow($column, $row)->getAlignment()->setWrapText(true);

                $column++;

                $sheet->setCellValueByColumnAndRow($column, $row, implode("\n", $usuarioAcceso));
                $sheet->getStyleByColumnAndRow($column, $row)->getAlignment()->setWrapText(true);

                $column++;

                $sheet->setCellValueByColumnAndRow($column, $row, implode("\n", $contraseniaAcceso));
                $sheet->getStyleByColumnAndRow($column, $row)->getAlignment()->setWrapText(true);

                $column++;
            } else {
                $column += 11;
            }

            // Personal asignado al equipo
            $usuarios = [];
            foreach ($equipo->getUsuarios() as $usuario) {
                if ($usuario->getIsActual()) {
                    $usuarios[] = $usuario->getUsuario()->getApellido() . ', ' . $usuario->getUsuario()->getNombre();
                }
            }
            $sheet->setCellValueByColumnAndRow($column, $row, implode("\n", $usuarios));
            $sheet->getStyleByColumnAndRow($column, $row)->getAlignment()->setWrapText(true);
            $column++;

            // Datos de Ubicación
            $ubicacion = $equipo->getUbicacion();
            $sheet->setCellValueByColumnAndRow($column++, $row, $ubicacion ? $ubicacion->getNomenclatura() : '-');


            // Aplicar centrado a toda la fila actual
            $rangeFila = "A{$row}:{$lastCol}{$row}";
            $sheet->getStyle($rangeFila)->applyFromArray($styleContenidoArray);

            $row++;
        }

        $lastRow = $row - 1;
        $rangeCompleto = "A1:{$lastCol}{$lastRow}";

        // Aplicar bordes a toda la tabla
        $sheet->getStyle($rangeCompleto)->applyFromArray($borderStyle);

        // Genera el archivo en memoria
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        return ob_get_clean();
    }
}

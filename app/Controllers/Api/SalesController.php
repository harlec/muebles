<?php

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\Local;
use App\Models\Producto;
use App\Models\Venta;

/**
 * Endpoints de datos crudos para las 3 vistas del dashboard.
 * Los cálculos derivados (metaPct, proyección, avanceDelDía, deltas, etc.)
 * se hacen en el front con las fórmulas de
 * design_handoff_dashboard_multilocal/README.md ("Fórmulas clave"),
 * igual que en el prototipo (clase Component.renderVals).
 */
class SalesController extends Controller
{
    /** Vista HOY: ventas de hoy, comparación con ayer, ranking y por hora. */
    public function today(): void
    {
        $localId = $this->localFiltro();
        $hoy = date('Y-m-d');
        $ayer = date('Y-m-d', strtotime('-1 day'));
        $horaActual = (int) date('G');

        $this->json([
            'fecha'      => $hoy,
            'local'      => $localId,
            'metaDiaria' => $this->metaDiaria($localId),
            'hoy'        => Venta::totalDia($hoy, $localId),
            'ayerCompleto'    => Venta::totalDia($ayer, $localId),
            'ayerHastaAhora'  => Venta::totalDia($ayer, $localId, $horaActual),
            'porHora'    => Venta::porHora($hoy, $localId),
            'ranking'    => Venta::rankingLocalesHoy($hoy),
            'feed'       => Venta::ultimas(5),
        ]);
    }

    /** Vista MES: serie diaria, meta mensual y proyección por local. */
    public function month(): void
    {
        $localId = $this->localFiltro();
        $anioMes = date('Y-m');

        $porLocal = [];
        foreach (Local::all() as $local) {
            $porLocal[] = $local + ['acumulado' => Venta::totalMes($anioMes, $local['id'])];
        }

        $this->json([
            'anioMes'     => $anioMes,
            'local'       => $localId,
            'diasDelMes'  => (int) date('t'),
            'diaActual'   => (int) date('j'),
            'metaMensual' => $this->metaMensual($localId),
            'serieMes'    => Venta::serieMes($anioMes, $localId),
            'porLocal'    => $porLocal,
        ]);
    }

    /** Mix de métodos de pago, para HOY o el MES en curso. */
    public function payments(): void
    {
        $localId = $this->localFiltro();
        $scope = $this->query('scope', 'today');

        if ($scope === 'month') {
            $desde = date('Y-m-01');
            $hasta = date('Y-m-d');
        } else {
            $desde = $hasta = date('Y-m-d');
        }

        $this->json([
            'scope' => $scope,
            'local' => $localId,
            'mix'   => Venta::mixMetodosPago($desde, $hasta, $localId),
        ]);
    }

    /** Vista ROTACIÓN: top de productos (30 días) y ventas por categoría. */
    public function rotation(): void
    {
        $localId = $this->localFiltro();
        $dias = (int) $this->query('dias', 30);

        $this->json([
            'dias'       => $dias,
            'local'      => $localId,
            'productos'  => Producto::masVendidos($dias, $localId),
            'categorias' => Producto::porCategoria($dias, $localId),
        ]);
    }

    /** Feed incremental de ventas ("ventas entrando"), para polling corto. */
    public function feed(): void
    {
        $desdeId = $this->query('since') !== null ? (int) $this->query('since') : null;

        $this->json([
            'ventas' => Venta::ultimas(5, $desdeId),
        ]);
    }

    private function localFiltro(): ?string
    {
        $local = $this->query('local', 'global');
        return $local === 'global' ? null : $local;
    }

    private function metaDiaria(?string $localId): float
    {
        if ($localId === null) {
            return (float) Local::metasGlobales()['meta_diaria'];
        }
        return (float) (Local::find($localId)['meta_diaria'] ?? 0);
    }

    private function metaMensual(?string $localId): float
    {
        if ($localId === null) {
            return (float) Local::metasGlobales()['meta_mensual'];
        }
        return (float) (Local::find($localId)['meta_mensual'] ?? 0);
    }
}

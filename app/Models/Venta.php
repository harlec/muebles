<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Venta
{
    /**
     * Ventas por hora (10..20) para una fecha dada. Alcance: un local o todos.
     * Devuelve [hora => ['monto' => float, 'tickets' => int]] con todas las horas presentes.
     */
    public static function porHora(string $fecha, ?string $localId = null): array
    {
        $sql = 'SELECT hora, SUM(monto) AS monto, COUNT(*) AS tickets
                FROM ventas WHERE fecha = ?';
        $params = [$fecha];

        if ($localId !== null) {
            $sql .= ' AND local_id = ?';
            $params[] = $localId;
        }

        $sql .= ' GROUP BY hora';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        $porHora = [];
        for ($h = 10; $h <= 20; $h++) {
            $porHora[$h] = ['monto' => 0.0, 'tickets' => 0];
        }
        foreach ($stmt->fetchAll() as $row) {
            $porHora[(int) $row['hora']] = [
                'monto'   => (float) $row['monto'],
                'tickets' => (int) $row['tickets'],
            ];
        }

        return $porHora;
    }

    /** Total de una fecha completa (o hasta una hora límite inclusive). */
    public static function totalDia(string $fecha, ?string $localId = null, ?int $hastaHora = null): array
    {
        $sql = 'SELECT COALESCE(SUM(monto),0) AS monto, COUNT(*) AS tickets FROM ventas WHERE fecha = ?';
        $params = [$fecha];

        if ($localId !== null) {
            $sql .= ' AND local_id = ?';
            $params[] = $localId;
        }
        if ($hastaHora !== null) {
            $sql .= ' AND hora <= ?';
            $params[] = $hastaHora;
        }

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();

        return ['monto' => (float) $row['monto'], 'tickets' => (int) $row['tickets']];
    }

    /** Serie diaria del mes (día => monto), para el gráfico "real vs proyección". */
    public static function serieMes(string $anioMes, ?string $localId = null): array
    {
        $sql = "SELECT DAY(fecha) AS dia, SUM(monto) AS monto
                FROM ventas WHERE DATE_FORMAT(fecha, '%Y-%m') = ?";
        $params = [$anioMes];

        if ($localId !== null) {
            $sql .= ' AND local_id = ?';
            $params[] = $localId;
        }

        $sql .= ' GROUP BY DAY(fecha) ORDER BY dia';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        $serie = [];
        foreach ($stmt->fetchAll() as $row) {
            $serie[(int) $row['dia']] = (float) $row['monto'];
        }

        return $serie;
    }

    /** Total acumulado del mes. */
    public static function totalMes(string $anioMes, ?string $localId = null): array
    {
        $sql = "SELECT COALESCE(SUM(monto),0) AS monto, COUNT(*) AS tickets
                FROM ventas WHERE DATE_FORMAT(fecha, '%Y-%m') = ?";
        $params = [$anioMes];

        if ($localId !== null) {
            $sql .= ' AND local_id = ?';
            $params[] = $localId;
        }

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();

        return ['monto' => (float) $row['monto'], 'tickets' => (int) $row['tickets']];
    }

    /** Mix de métodos de pago en un rango de fechas. */
    public static function mixMetodosPago(string $desde, string $hasta, ?string $localId = null): array
    {
        $sql = 'SELECT metodo_pago, SUM(monto) AS monto, COUNT(*) AS tickets
                FROM ventas WHERE fecha BETWEEN ? AND ?';
        $params = [$desde, $hasta];

        if ($localId !== null) {
            $sql .= ' AND local_id = ?';
            $params[] = $localId;
        }

        $sql .= ' GROUP BY metodo_pago';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /** Últimas ventas para el feed en vivo. */
    public static function ultimas(int $limite = 5, ?int $desdeId = null): array
    {
        $sql = "SELECT v.id, v.hora, v.monto, v.metodo_pago, v.creado_en,
                       l.nombre AS local_nombre, p.nombre AS producto_nombre
                FROM ventas v
                JOIN locales l ON l.id = v.local_id
                JOIN productos p ON p.id = v.producto_id";
        $params = [];

        if ($desdeId !== null) {
            $sql .= ' WHERE v.id > ?';
            $params[] = $desdeId;
        }

        $sql .= ' ORDER BY v.id DESC LIMIT ' . (int) $limite;

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /** Ranking de ventas de hoy por local (para "quién vende más hoy"). */
    public static function rankingLocalesHoy(string $fecha): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT l.id, l.nombre, l.meta_diaria,
                    COALESCE(SUM(v.monto), 0) AS monto,
                    COUNT(v.id) AS tickets
             FROM locales l
             LEFT JOIN ventas v ON v.local_id = l.id AND v.fecha = ?
             WHERE l.activo = 1
             GROUP BY l.id, l.nombre, l.meta_diaria
             ORDER BY monto DESC'
        );
        $stmt->execute([$fecha]);

        return $stmt->fetchAll();
    }
}

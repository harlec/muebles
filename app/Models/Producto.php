<?php

namespace App\Models;

use App\Core\Database;

class Producto
{
    /**
     * Top de productos vendidos en una ventana de N días, con índice de
     * rotación e inventario estimado (requiere columna de stock real:
     * ver nota en README de este proyecto sobre `stock_actual`).
     */
    public static function masVendidos(int $dias = 30, ?string $localId = null, int $limite = 10): array
    {
        $sql = "SELECT p.id, p.nombre, c.nombre AS categoria,
                       SUM(1) AS unidades,
                       SUM(v.monto) AS monto,
                       AVG(v.monto) AS precio_promedio
                FROM ventas v
                JOIN productos p ON p.id = v.producto_id
                JOIN categorias c ON c.id = p.categoria_id
                WHERE v.fecha >= DATE_SUB(CURDATE(), INTERVAL ? DAY)";
        $params = [$dias];

        if ($localId !== null) {
            $sql .= ' AND v.local_id = ?';
            $params[] = $localId;
        }

        $sql .= ' GROUP BY p.id, p.nombre, c.nombre ORDER BY unidades DESC LIMIT ' . (int) $limite;

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /** Ventas agrupadas por categoría en una ventana de días. */
    public static function porCategoria(int $dias, ?string $localId = null): array
    {
        $sql = "SELECT c.nombre AS categoria, SUM(1) AS unidades, SUM(v.monto) AS monto
                FROM ventas v
                JOIN productos p ON p.id = v.producto_id
                JOIN categorias c ON c.id = p.categoria_id
                WHERE v.fecha >= DATE_SUB(CURDATE(), INTERVAL ? DAY)";
        $params = [$dias];

        if ($localId !== null) {
            $sql .= ' AND v.local_id = ?';
            $params[] = $localId;
        }

        $sql .= ' GROUP BY c.nombre ORDER BY monto DESC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}

<?php

namespace App\Models;

use App\Core\Database;

class Local
{
    public static function all(): array
    {
        $stmt = Database::connection()->query(
            'SELECT id, nombre, meta_diaria, meta_mensual FROM locales WHERE activo = 1 ORDER BY meta_diaria DESC'
        );

        return $stmt->fetchAll();
    }

    public static function find(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT id, nombre, meta_diaria, meta_mensual FROM locales WHERE id = ? AND activo = 1'
        );
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    /** Suma de metas diaria/mensual, para el alcance "global" (los 4 locales). */
    public static function metasGlobales(): array
    {
        $stmt = Database::connection()->query(
            'SELECT SUM(meta_diaria) AS meta_diaria, SUM(meta_mensual) AS meta_mensual FROM locales WHERE activo = 1'
        );

        return $stmt->fetch();
    }
}

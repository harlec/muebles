<?php

// En Plesk: define estas variables de entorno en el panel del dominio
// (Sitios web y dominios > tu dominio > PHP > Variables de entorno),
// o edita directamente los valores por defecto de abajo antes de subir.

return [
    'db' => [
        'host'    => getenv('DB_HOST') ?: '127.0.0.1',
        'name'    => getenv('DB_NAME') ?: 'muebles_dashboard',
        'user'    => getenv('DB_USER') ?: 'root',
        'pass'    => getenv('DB_PASS') ?: '',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'timezone'      => date_default_timezone_get() ?: 'America/Mexico_City',
        'currency'      => getenv('APP_CURRENCY') ?: '$ ', // ej. "S/ " para soles
        'hora_apertura' => 10,
        'hora_cierre'   => 20,
    ],
];

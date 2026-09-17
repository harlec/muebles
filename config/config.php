<?php

// En Plesk: define estas variables de entorno en el panel del dominio
// (Sitios web y dominios > tu dominio > PHP > Variables de entorno).
//
// Si tu plan de Plesk no expone variables de entorno por dominio, crea
// config/config.local.php (copiando config.local.php.example) con los
// valores reales. Ese archivo está en .gitignore a propósito: así un
// `git pull` en el servidor nunca pisa tus credenciales de producción.

$config = [
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
        'layout_width'  => getenv('APP_LAYOUT_WIDTH') ?: 'completo', // compacto | amplio | completo
    ],
];

$localOverride = __DIR__ . '/config.local.php';
if (is_file($localOverride)) {
    $config = array_replace_recursive($config, require $localOverride);
}

return $config;

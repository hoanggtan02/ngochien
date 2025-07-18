<?php
if (!defined('ECLO')) die("Hacking attempt");

$env = parse_ini_file(__DIR__ . '/../.env');
$api_transports = [
    "1" => [
        "id" => 1,
        "name" => "Không Tích Hợp",
        "code" => 'KTH',
        "logo" => '',
    ],
    "2" => [
        "id" => 2,
        "name" => "Giao Hàng Nhanh",
        "code" => 'GHN',
        "logo" => '',
        "API" => [
            "token" => 'Token',
        ],
    ],
];
$payment_type = [
    "1" => [
        "name" => 'Bán hàng',
        "id" => 1,
    ],
    "2" => [
        "name" =>  'Trả hàng',
        "id" => 2,
    ],
    "3" => [
        "name" =>  'Bảo hành',
        "id" => 2,
    ],
];

return [
    "db" => [
        'type'      => $env['DB_TYPE'] ?? 'mysql',
        'host'      => $env['DB_HOST'] ?? 'localhost',
        'database'  => $env['DB_DATABASE'] ?? 'default_database',
        'username'  => $env['DB_USERNAME'] ?? 'default_user',
        'password'  => $env['DB_PASSWORD'] ?? '',
        'charset'   => $env['DB_CHARSET'] ?? 'utf8mb4',
        'collation' => $env['DB_COLLATION'] ?? 'utf8mb4_general_ci',
        'port'      => (int) ($env['DB_PORT'] ?? 3306),
        'prefix'    => $env['DB_PREFIX'] ?? '',
        'logging'   => filter_var($env['DB_LOGGING'] ?? 'false', FILTER_VALIDATE_BOOLEAN),
        'error'     => constant('PDO::' . ($env['DB_ERROR'] ?? 'ERRMODE_SILENT')),
        'option'    => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL,
        ],
        'command'   => [
            'SET SQL_MODE=ANSI_QUOTES'
        ]
    ],
    "app" => [
        "url"        => 'https://test.ngochienpearl.com',
        "name"       => 'Ngọc Hiền Pearl ERP',
        "page"       => 12,
        "manager"    => '',
        "template"   => '../templates',
        "secret-key" => '19a3d43a4df700dc5d35f6a7a69e5e79d522d91784e66bdaa2fa475731ae0abc31363138323237313233',
        "verifier"   => 'emejRcfqO2sFkARMmUy0tvE003Y3i9tyVNwcaE4J7Y7',
        "cookie"     => (3600 * 24 * 30) * 12, // 1 năm
        "lang"       => $_COOKIE['lang'] ?? 'vi',
        "plugins"    => '../plugins',
        "uploads"    => '../datas',
        "api"        => $api_transports,
        "type-payment"      => $payment_type,
        ]
];

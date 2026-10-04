<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Datos del proveedor
    |--------------------------------------------------------------------------
    | Se muestran en el formulario, en la constancia enviada al consumidor y en
    | el reporte PDF. Configúralos en el archivo .env.
    */

    'razon_social'     => env('EMPRESA_RAZON_SOCIAL', 'Empresa Demo S.A.C.'),
    'nombre_comercial' => env('EMPRESA_NOMBRE_COMERCIAL', 'Empresa Demo'),
    'ruc'              => env('EMPRESA_RUC', '20000000001'),
    'direccion'        => env('EMPRESA_DIRECCION', 'Av. Ejemplo 123, Lima, Perú'),
    'color'            => env('EMPRESA_COLOR', '#1d4ed8'),

    /*
    |--------------------------------------------------------------------------
    | Plazo de respuesta
    |--------------------------------------------------------------------------
    | Días hábiles que tiene el proveedor para responder un reclamo o queja.
    */

    'plazo_dias_habiles' => (int) env('RECLAMOS_PLAZO_DIAS_HABILES', 15),

    /*
    |--------------------------------------------------------------------------
    | Administrador inicial (lo usa php artisan db:seed)
    |--------------------------------------------------------------------------
    */

    'admin' => [
        'name'     => env('ADMIN_NAME', 'Administrador'),
        'email'    => env('ADMIN_EMAIL', 'admin@empresademo.pe'),
        'password' => env('ADMIN_PASSWORD'),
    ],

];

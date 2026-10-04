<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Libro de Reclamaciones') · {{ config('empresa.nombre_comercial') }}</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'><path fill='%231d4ed8' d='M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687z'/></svg>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand: {{ config('empresa.color') }};
            --brand-soft: color-mix(in srgb, var(--brand) 10%, white);
            --brand-dark: color-mix(in srgb, var(--brand) 80%, black);
        }
        body { font-family: 'Inter', system-ui, sans-serif; background: #f4f6f9; min-height: 100vh; display: flex; flex-direction: column; }
        .text-brand { color: var(--brand) !important; }
        .bg-brand { background: var(--brand) !important; }
        .bg-brand-soft { background: var(--brand-soft) !important; }
        .btn-brand { background: var(--brand); color: #fff; font-weight: 600; border: none; }
        .btn-brand:hover, .btn-brand:focus { background: var(--brand-dark); color: #fff; }
        .btn-outline-brand { color: var(--brand); border-color: var(--brand); }
        .btn-outline-brand:hover, .btn-check:checked + .btn-outline-brand { background: var(--brand); border-color: var(--brand); color: #fff; }
        .form-control:focus, .form-select:focus, .form-check-input:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 .2rem color-mix(in srgb, var(--brand) 25%, transparent);
        }
        .form-check-input:checked { background-color: var(--brand); border-color: var(--brand); }
        .card { border: none; border-radius: .9rem; box-shadow: 0 .25rem 1rem rgba(15, 23, 42, .08); }
    </style>
    @stack('styles')
    @livewireStyles
</head>
<body>
    @yield('body')
    @livewireScripts
</body>
</html>

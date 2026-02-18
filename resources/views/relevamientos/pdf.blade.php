<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Relevamiento – {{ $relevamiento->titulorelevamiento }}</title>
    <style>
        body      { font-family: DejaVu Sans, sans-serif; color:#333; }
        h1        { font-size:24pt; text-align:center; margin:10px 0 20px }
        h1 small  { font-size:12pt; font-weight:normal; color:#666 }
        p         { font-size:11pt; line-height:1.4; text-align:justify; margin:20px 0 }
        .image    { text-align:center; margin:30px 0 }
        .image img{ max-width:450px; height:auto }
        footer    { position:fixed; bottom:15px; left:0; right:0; text-align:center; font-size:9pt; color:#999 }
    </style>
</head>
<body>

    {{-- Logo opcional (comenta si no lo necesitas) --}}
    <div style="text-align:center; margin-bottom:20px">
        <img src="{{ public_path('img/logo-raracuenca.png') }}" alt="Logo" height="150">
    </div>

    <h1>
        {{ $relevamiento->titulorelevamiento }}
        <br>
        <small>
            {{ $relevamiento->equipo }} – {{ \Carbon\Carbon::parse($relevamiento->fechacarga)->format('d/m/Y') }}
        </small>
    </h1>

    <p>{!! nl2br(e($relevamiento->detallerelevamiento)) !!}</p>

    @if($relevamiento->foto1)
        <div class="image">
            <img src="{{ storage_path('app/public/relevamientos/'.$relevamiento->foto1) }}" alt="Imagen del relevamiento">
        </div>
    @endif

    <footer>RaraCuenca APP</footer>
</body>
</html>

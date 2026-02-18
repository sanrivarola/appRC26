{{-- resources/views/components/pluviometro-chart.blade.php --}}
@props(['datos', 'agrupado' => 'dias'])

@php
    $labels = collect($datos)->keys()->map(function ($key) use ($agrupado) {
        return $agrupado === 'meses'
            ? \Carbon\Carbon::createFromFormat('Y-m', $key)->format('M Y')
            : \Carbon\Carbon::parse($key)->format('d/m');
    })->toArray();

    $values = array_values($datos->toArray());
@endphp

@if(count($values))
  <canvas id="rainChart" height="120"></canvas>

  @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
      new Chart(document.getElementById('rainChart'), {
        type : 'bar',
        data : {
          labels   : @json($labels),
          datasets : [{
            label : 'mm de lluvia',
            data  : @json($values),
            borderWidth : 1
          }]
        },
        options : { scales : { y : { beginAtZero : true } } }
      });
    </script>
  @endpush
@endif

{{-- resources/views/compras/_tabla.blade.php --}}
@php
  $current   = request()->all();
  $sort      = request('sort', 'fechacarga');
  $direction = request('direction', 'desc');

  $arrow = function ($col) use ($sort, $direction) {
    return $sort === $col ? ($direction === 'asc' ? ' ▲' : ' ▼') : '';
  };

  $nextDir = function ($col) use ($sort, $direction) {
    return ($sort === $col && $direction === 'asc') ? 'desc' : 'asc';
  };

  $sortUrl = function ($col) use ($current, $nextDir) {
    $params = collect($current)->except(['page','realizar','proceso'])->toArray();
    $params['sort']      = $col;
    $params['direction'] = $nextDir($col);
    return url()->current() . '?' . http_build_query($params);
  };
@endphp

<div class="overflow-x-auto bg-white rounded shadow">
  <table class="w-full border-collapse">
    <thead>
      <tr class="bg-slate-100 text-left text-sm font-medium text-slate-600">
        <th class="px-4 py-2">
          <a href="{{ $sortUrl('fechacarga') }}" class="hover:underline">Fecha{{ $arrow('fechacarga') }}</a>
        </th>
        <th class="px-4 py-2">
          <a href="{{ $sortUrl('area_id') }}" class="hover:underline">Área{{ $arrow('area_id') }}</a>
        </th>
        <th class="px-4 py-2">
          <a href="{{ $sortUrl('producto') }}" class="hover:underline">Producto{{ $arrow('producto') }}</a>
        </th>
        <th class="px-4 py-2">
          <a href="{{ $sortUrl('prioridad') }}" class="hover:underline">Prioridad{{ $arrow('prioridad') }}</a>
        </th>
        <th class="px-4 py-2">
          <a href="{{ $sortUrl('estado') }}" class="hover:underline">Estado{{ $arrow('estado') }}</a>
        </th>
        <th class="px-4 py-2">
          <a href="{{ $sortUrl('responsable') }}" class="hover:underline">Responsable{{ $arrow('responsable') }}</a>
        </th>
        <th class="px-4 py-2 text-right">
          <a href="{{ $sortUrl('id_compras') }}" class="hover:underline">#{{ $arrow('id_compras') }}</a>
        </th>
        <th class="px-4 py-2 text-right">Acciones</th>
      </tr>
    </thead>

    <tbody>
      @forelse($items as $c)
        @php
          $estadoNorm  = trim(mb_strtolower($c->estado ?? '', 'UTF-8'));
          $esRealizada = in_array($estadoNorm, ['compra realizada','realizada'], true);
        @endphp
        <tr class="border-b hover:bg-slate-50">
          <td class="px-4 py-2 text-sm">
            {{ optional($c->fechacarga)->format('d/m/Y') }}
          </td>
          <td class="px-4 py-2 text-sm">
            {{ $c->area->nombre ?? '—' }}
          </td>
          <td class="px-4 py-2 text-sm">
            {{ \Illuminate\Support\Str::limit($c->producto, 60) }}
          </td>
          <td class="px-4 py-2 text-sm">
            <span @class([
              'inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full',
              'bg-red-200 text-red-700'       => $c->prioridad==='Alta',
              'bg-yellow-200 text-yellow-700' => $c->prioridad==='Media',
              'bg-green-200 text-green-700'   => $c->prioridad==='Baja',
            ])>
              <i class="fa-solid fa-flag"></i> {{ $c->prioridad }}
            </span>
          </td>
          <td class="px-4 py-2 text-sm">
            <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full {{ $esRealizada ? 'bg-green-200 text-green-700' : 'bg-yellow-200 text-yellow-700' }}">
              <i class="fa-solid fa-circle"></i> {{ $c->estado }}
            </span>
          </td>
          <td class="px-4 py-2 text-sm">
            {{ $c->responsable }}
          </td>
          <td class="px-4 py-2 text-sm text-right">
            {{ $c->id_compras }}
          </td>
          <td class="px-4 py-2 text-sm text-right">
            <a href="{{ route('compras.edit', $c->id_compras) }}"
               class="text-sky-600 hover:underline">Editar</a>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="8" class="px-4 py-6 text-center text-slate-500">
            {{ $vacío ?? 'Sin datos' }}
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

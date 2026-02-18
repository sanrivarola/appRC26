<table class="w-full bg-white rounded shadow overflow-hidden">
  <thead class="bg-slate-100 text-slate-700 text-sm">
    <tr>
      <th class="text-left p-3">#</th>
      <th class="text-left p-3">Fecha</th>
      <th class="text-left p-3">Área</th>
      <th class="text-left p-3">Producto</th>
      <th class="text-left p-3">Prioridad</th>
      <th class="text-left p-3">Estado</th>
      <th class="text-left p-3">Responsable</th>
      <th class="text-right p-3">Acciones</th>
    </tr>
  </thead>
  <tbody class="divide-y">
    @forelse($items as $c)
      <tr class="hover:bg-slate-50">
        <td class="p-3 text-slate-700">{{ $c->id_compras }}</td>
        <td class="p-3 text-slate-700">
          {{ optional($c->fechacarga)->format('d/m/Y') }}
        </td>
        <td class="p-3">
          {{-- Puede venir null si no hubo mapeo --}}
          <span class="text-slate-700">
            {{ $c->area->nombre ?? '—' }}
          </span>
        </td>
        <td class="p-3 text-slate-800">
          {{ \Illuminate\Support\Str::limit($c->producto, 70) }}
        </td>
        <td class="p-3">
          <span @class([
            'inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full',
            'bg-red-200 text-red-700'      => $c->prioridad==='Alta',
            'bg-yellow-200 text-yellow-700'=> $c->prioridad==='Media',
            'bg-green-200 text-green-700'  => $c->prioridad==='Baja',
          ])>
            <i class="fa-solid fa-flag"></i> {{ $c->prioridad }}
          </span>
        </td>
        <td class="p-3">
          <span @class([
            'inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full',
            'bg-green-200 text-green-700' => $c->estado==='Compra realizada',
            'bg-amber-200 text-amber-700' => $c->estado==='Compra en proceso',
            'bg-red-200 text-red-700'     => $c->estado==='Compra a realizar',
          ])>
            <i class="fa-solid fa-circle"></i> {{ $c->estado }}
          </span>
        </td>
        <td class="p-3 text-slate-700">{{ $c->responsable }}</td>
        <td class="p-3 text-right">
          <a href="{{ route('compras.legacy.show', $c->id_compras) }}"
             class="text-sky-700 hover:underline">Ver</a>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="8" class="p-6 text-center text-slate-500">{{ $vacio ?? 'Sin datos' }}</td>
      </tr>
    @endforelse
  </tbody>
</table>

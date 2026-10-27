{{-- resources/views/compras/_tabla_historial.blade.php --}}
{{-- Vista dedicada al historial (realizadas + no realizadas) --}}

<div class="overflow-x-auto bg-white rounded-xl shadow">
  <table class="w-full border-collapse text-sm">
    <thead>
      <tr class="bg-slate-100 text-left text-xs font-semibold text-slate-600 uppercase tracking-wide">
        <th class="px-4 py-3">Fecha carga</th>
        <th class="px-4 py-3">Área</th>
        <th class="px-4 py-3">Subárea</th>
        <th class="px-4 py-3">Producto</th>
        <th class="px-4 py-3">Cant.</th>
        <th class="px-4 py-3">Prioridad</th>
        <th class="px-4 py-3">Estado</th>
        <th class="px-4 py-3">Responsable</th>
        <th class="px-4 py-3 text-right">Acciones</th>
      </tr>
    </thead>
    <tbody>
      @forelse($compras as $c)
        @php
          $estadoNorm  = trim(mb_strtolower($c->estado ?? '', 'UTF-8'));
          $esRealizada = $estadoNorm === 'compra realizada';
          $esCancelada = $estadoNorm === 'compra no realizada';
        @endphp

        <tr class="border-b transition-colors
          {{ $esRealizada ? 'bg-green-50 hover:bg-green-100' : 'bg-red-50 hover:bg-red-100' }}">

          {{-- Fecha carga --}}
          <td class="px-4 py-3 text-slate-600">
            {{ optional($c->fechacarga)->format('d/m/Y') }}
          </td>

          {{-- Área --}}
          <td class="px-4 py-3">{{ $c->area->nombre ?? '—' }}</td>

          {{-- Subárea --}}
          <td class="px-4 py-3">{{ $c->subarea->nombre ?? '—' }}</td>

          {{-- Producto + motivo cancelación si aplica --}}
          <td class="px-4 py-3">
            <div class="font-medium text-slate-800">
              {{ \Illuminate\Support\Str::limit($c->producto, 55) }}
            </div>
            @if($esCancelada && $c->motivo_cancelacion)
              <div class="mt-1 flex items-start gap-1 text-xs text-red-600">
                <span class="italic">{{ $c->motivo_cancelacion }}</span>
              </div>
            @endif
          </td>

          {{-- Cantidad --}}
          <td class="px-4 py-3 text-slate-600">{{ $c->cantidad }}</td>

          {{-- Prioridad --}}
          <td class="px-4 py-3">
            <span @class([
              'inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full font-medium',
              'bg-red-200 text-red-700'       => $c->prioridad==='Alta',
              'bg-yellow-200 text-yellow-700' => $c->prioridad==='Media',
              'bg-green-200 text-green-700'   => $c->prioridad==='Baja',
            ])>
              <i class="fa-solid fa-flag"></i> {{ $c->prioridad }}
            </span>
          </td>

          {{-- Estado --}}
          <td class="px-4 py-3">
            @if($esRealizada)
              <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-green-200 text-green-800 font-semibold">
                <i class="fa-solid fa-circle-check"></i> Realizada
              </span>
            @else
              <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-red-200 text-red-800 font-semibold">
                <i class="fa-solid fa-circle-xmark"></i> No realizada
              </span>
            @endif
          </td>

          {{-- Responsable --}}
          <td class="px-4 py-3 text-slate-600">{{ $c->responsable }}</td>

          {{-- Acciones --}}
          <td class="px-4 py-3 text-right">
            <a href="{{ route('compras.show', $c->id_compras) }}"
               class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-100 transition">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                   stroke-width="1.5" stroke="currentColor" class="size-3.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.641 0-8.572-3.007-9.963-7.178Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
              </svg>
              Ver detalle
            </a>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="9" class="px-4 py-10 text-center text-slate-400">
            No hay compras en el historial.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
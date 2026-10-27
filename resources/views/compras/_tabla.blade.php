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
          Subárea
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
          $esCancelada = $estadoNorm === 'compra no realizada';
          $esPendiente = !$esRealizada && !$esCancelada;
        @endphp
        <tr class="border-b hover:bg-slate-50">
          <td class="px-4 py-2 text-sm">
            {{ optional($c->fechacarga)->format('d/m/Y') }}
          </td>
          <td class="px-4 py-2 text-sm">
            {{ $c->area->nombre ?? '—' }}
          </td>
          <td class="px-4 py-2 text-sm">
            {{ $c->subarea->nombre ?? '—' }}
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
            <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full
              {{ $esRealizada ? 'bg-green-200 text-green-700' : ($esCancelada ? 'bg-red-200 text-red-700' : 'bg-yellow-200 text-yellow-700') }}">
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
            <div class="flex items-center justify-end gap-3">
              @if(!$esCancelada)
                <a href="{{ route('compras.edit', $c->id_compras) }}"
                   class="text-sky-600 hover:underline">Editar</a>
              @endif

              @if($esPendiente)
                @role('admin|editor')
                <button type="button"
                        onclick="abrirModalCancelar({{ $c->id_compras }}, @js(\Illuminate\Support\Str::limit($c->producto, 40)))"
                        class="text-red-500 hover:text-red-700 hover:underline text-xs font-medium">
                  Cancelar
                </button>
                @endrole
              @endif

              @if($esCancelada && $c->motivo_cancelacion)
                <span class="text-xs text-slate-400 italic" title="{{ $c->motivo_cancelacion }}">
                  "{{ \Illuminate\Support\Str::limit($c->motivo_cancelacion, 35) }}"
                </span>
              @endif
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="9" class="px-4 py-6 text-center text-slate-500">
            {{ $vacío ?? 'Sin datos' }}
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- MODAL CANCELAR --}}
<div id="modalCancelar"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
  <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md mx-4">
    <div class="flex items-center gap-3 mb-4">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
           stroke="currentColor" class="size-6 text-red-500">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
      </svg>
      <h3 class="text-lg font-bold text-slate-800">Cancelar compra</h3>
    </div>

    <p class="text-sm text-slate-600 mb-1">Producto:</p>
    <p id="modalProductoNombre" class="font-semibold text-slate-800 mb-4"></p>

    <p class="text-sm text-slate-500 mb-4">
      La compra pasará al historial como <span class="font-semibold text-red-600">no realizada</span>.
      Esta acción no se puede deshacer.
    </p>

    <form id="formCancelar" method="POST">
      @csrf
      @method('PUT')
      <div class="mb-4">
        <label class="block text-sm font-medium text-slate-700 mb-1">
          Motivo de cancelación <span class="text-red-500">*</span>
        </label>
        <textarea name="motivo_cancelacion"
                  rows="3"
                  required
                  maxlength="1000"
                  placeholder="Ej: Presupuesto insuficiente, producto ya no es necesario..."
                  class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-red-400 focus:border-red-400 resize-none"></textarea>
      </div>

      <div class="flex gap-3 justify-end">
        <button type="button"
                onclick="cerrarModalCancelar()"
                class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition text-sm font-semibold">
          Volver
        </button>
        <button type="submit"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-semibold">
          Sí, cancelar compra
        </button>
      </div>
    </form>
  </div>
</div>

<script>
var cancelarBaseUrl = '{{ url("compras") }}';
function abrirModalCancelar(id, producto) {
  document.getElementById('modalProductoNombre').textContent = producto;
  document.getElementById('formCancelar').action = cancelarBaseUrl + '/' + id + '/cancelar';
  document.getElementById('modalCancelar').classList.remove('hidden');
}
function cerrarModalCancelar() {
  document.getElementById('modalCancelar').classList.add('hidden');
}
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') cerrarModalCancelar();
});
</script>
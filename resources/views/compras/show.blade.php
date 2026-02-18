<x-app-layout>
  <div class="flex" style="color-scheme: light;">
    <x-sidebar />

    <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-6 md:p-10 text-slate-800">
      <div class="max-w-3xl mx-auto bg-white shadow rounded-xl p-8 space-y-8">

        {{-- CABECERA --}}
        <div class="flex items-center justify-between">
          <h1 class="text-2xl font-bold text-sky-700 flex items-center gap-2">
            <i class="fa-solid fa-cart-shopping"></i>
            Detalle de compra
          </h1>

          <div class="flex items-center gap-2">
            <a href="{{ url()->previous() }}"
               class="px-4 py-2 bg-slate-500 text-white rounded hover:bg-slate-600">
              Volver
            </a>
            @role('admin|editor')
              <a href="{{ route('compras.edit', $compra->id_compras) }}"
                 class="px-4 py-2 bg-sky-600 text-white rounded hover:bg-sky-700">
                Editar
              </a>
            @endrole
          </div>
        </div>

        {{-- INFO PRINCIPAL --}}
        <div class="grid md:grid-cols-2 gap-6">
          {{-- Área --}}
          <div>
            <div class="text-sm text-slate-500">Área del producto</div>
            <div class="mt-1 font-medium">{{ $compra->area->nombre ?? '—' }}</div>
          </div>

          {{-- Proveedor --}}
          <div>
            <div class="text-sm text-slate-500">Proveedor</div>
            <div class="mt-1 font-medium">
              @if($compra->proveedor)
                {{ $compra->proveedor->nombre }}
              @else
                @role('admin|editor')
                  <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-yellow-200 text-yellow-800">
                    <i class="fa-solid fa-triangle-exclamation"></i> Sin proveedor
                  </span>
                  <a href="{{ route('compras.edit', $compra->id_compras) }}"
                     class="ml-2 text-sm text-sky-700 hover:underline">
                    Asignar proveedor
                  </a>
                @else
                  —
                @endrole
              @endif
            </div>
          </div>

          {{-- Prioridad --}}
          <div>
            <div class="text-sm text-slate-500">Prioridad</div>
            <div class="mt-1">
              <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full
                @if($compra->prioridad==='Alta') bg-red-200 text-red-700
                @elseif($compra->prioridad==='Media') bg-yellow-200 text-yellow-700
                @else bg-green-200 text-green-700 @endif">
                <i class="fa-solid fa-flag"></i> {{ $compra->prioridad }}
              </span>
            </div>
          </div>

          {{-- Estado --}}
          <div>
            <div class="text-sm text-slate-500">Estado</div>
            <div class="mt-1">
              <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full
                @if($compra->estado==='Compra realizada') bg-green-200 text-green-700
                @elseif($compra->estado==='Compra en proceso') bg-amber-200 text-amber-700
                @else bg-red-200 text-red-700 @endif">
                <i class="fa-solid fa-circle"></i> {{ $compra->estado }}
              </span>
            </div>
          </div>

          {{-- Cantidad --}}
          <div>
            <div class="text-sm text-slate-500">Cantidad</div>
            <div class="mt-1 font-medium">{{ $compra->cantidad }}</div>
          </div>

          {{-- Responsable --}}
          <div>
            <div class="text-sm text-slate-500">Responsable de la compra</div>
            <div class="mt-1 font-medium">{{ $compra->responsable }}</div>
          </div>

          {{-- Fecha de carga --}}
          <div>
            <div class="text-sm text-slate-500">Fecha de carga</div>
            <div class="mt-1 font-medium">
              {{ \Carbon\Carbon::parse($compra->fechacarga)->format('d/m/Y') }}
            </div>
          </div>

          {{-- Fecha de compra --}}
          <div>
            <div class="text-sm text-slate-500">Fecha de compra</div>
            <div class="mt-1 font-medium">
              {{ $compra->fechacompra ? \Carbon\Carbon::parse($compra->fechacompra)->format('d/m/Y') : '—' }}
            </div>
          </div>
        </div>

        {{-- Producto (descripción) --}}
        <div>
          <div class="text-sm text-slate-500">Producto</div>
          <div class="mt-2 p-4 bg-slate-50 rounded border border-slate-200 leading-relaxed">
            {{ $compra->producto }}
          </div>
        </div>

      </div>
      
      {{-- HISTORIAL DE MOVIMIENTOS --}}
<div class="mt-10">
  <h2 class="text-lg font-semibold text-slate-700 mb-3">
    Historial de movimientos
  </h2>

  @php $movs = $compra->movimientos; @endphp

  @if($movs->isEmpty())
    <div class="text-slate-500">Sin movimientos registrados.</div>
  @else
    <div class="space-y-3">
      @foreach($movs as $m)
        <div class="border rounded p-3 bg-slate-50">
          <div class="flex items-center justify-between">
            <div class="text-sm text-slate-600">
              <strong>{{ $m->accion }}</strong> por {{ $m->user->name ?? 'Sistema' }}
            </div>
            <div class="text-xs text-slate-500">
              {{ $m->created_at?->format('d/m/Y H:i') }}
            </div>
          </div>

          @if($m->comentario)
            <div class="mt-1 text-sm">{{ $m->comentario }}</div>
          @endif

          @if(is_array($m->cambios) && !empty($m->cambios))
            <table class="mt-2 w-full text-xs">
              <thead>
                <tr class="text-slate-500">
                  <th class="text-left p-1">Campo</th>
                  <th class="text-left p-1">Antes</th>
                  <th class="text-left p-1">Después</th>
                </tr>
              </thead>
              <tbody>
                @foreach($m->cambios as $campo => $vals)
                  <tr class="border-t">
                    <td class="p-1 font-medium">{{ $campo }}</td>
                    <td class="p-1">{{ is_scalar($vals[0]) ? $vals[0] : json_encode($vals[0], JSON_UNESCAPED_UNICODE) }}</td>
                    <td class="p-1">{{ is_scalar($vals[1]) ? $vals[1] : json_encode($vals[1], JSON_UNESCAPED_UNICODE) }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </div>
      @endforeach
    </div>
  @endif
</div>



    </main>
  </div>
</x-app-layout>

{{-- resources/views/inventario/history.blade.php --}}
<x-app-layout>
  <div class="flex">
    <x-sidebar />

    <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-4 md:p-8">
      <div class="max-w-4xl mx-auto space-y-6">

        {{-- Cabecera --}}
        <div class="bg-white shadow rounded-2xl p-6 flex items-center justify-between">
          <h2 class="text-2xl font-semibold text-gray-700">
            Historial de <span class="font-bold">{{ $inventario->nombreproducto }}</span>
          </h2>
          <a href="{{ route('inventario.index') }}"
             class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded">
            ← Volver
          </a>
        </div>

        {{-- Datos del producto (deshabilitados) --}}
        <div class="bg-white shadow rounded-2xl p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-600">Producto</label>
            <input type="text" disabled
                   value="{{ $inventario->nombreproducto }}"
                   class="w-full border-gray-300 rounded px-3 py-2 bg-gray-100" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600">Área</label>
            <input type="text" disabled
                   value="{{ $inventario->area->nombre ?? '—' }}"
                   class="w-full border-gray-300 rounded px-3 py-2 bg-gray-100" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600">Subárea</label>
            <input type="text" disabled
                   value="{{ $inventario->subarea->nombre ?? '—' }}"
                   class="w-full border-gray-300 rounded px-3 py-2 bg-gray-100" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600">Cantidad actual</label>
            <input type="text" disabled
                   value="{{ $inventario->cantidad }}"
                   class="w-full border-gray-300 rounded px-3 py-2 bg-gray-100 text-center" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600">Stock mínimo</label>
            <input type="text" disabled
                   value="{{ $inventario->stockminimo }}"
                   class="w-full border-gray-300 rounded px-3 py-2 bg-gray-100 text-center" />
          </div>
          @if($inventario->vencimiento)
            <div>
              <label class="block text-sm font-medium text-gray-600">Vencimiento</label>
              <input type="date" disabled
                     value="{{ $inventario->vencimiento }}"
                     class="w-full border-gray-300 rounded px-3 py-2 bg-gray-100" />
            </div>
          @endif
          @if($inventario->detalle)
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-gray-600">Detalle</label>
              <textarea disabled rows="3"
                        class="w-full border-gray-300 rounded px-3 py-2 bg-gray-100">{{ $inventario->detalle }}</textarea>
            </div>
          @endif
        </div>

        {{-- Tabla historial --}}
        <div class="bg-white shadow rounded-2xl p-6 overflow-x-auto">
          <table class="min-w-full text-sm divide-y divide-gray-200">
            <thead class="bg-gray-100 text-gray-700">
              <tr>
                <th class="p-2 text-left">Operación</th>
                <th class="p-2 text-center">Cantidad</th>
                <th class="p-2 text-left">Equipo</th>
                <th class="p-2 text-left">Fecha</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              @forelse($historial as $h)
                <tr class="hover:bg-gray-50">
                  <td class="p-2">{{ ucfirst($h->tipo_operacion) }}</td>
                  <td class="p-2 text-center">{{ $h->cantidad_operacion }}</td>
                  <td class="p-2">{{ $h->equipo }}</td>
                  <td class="px-3 py-1">
                  @if($h->link_origen)
                    <a href="{{ $h->link_origen }}" target="_blank"
                        class="text-blue-600 hover:underline">
                        {{ \Carbon\Carbon::parse($h->fecha)->format('d/m/Y') }}
                    </a>
                  @else
                    {{ \Carbon\Carbon::parse($h->fecha)->format('d/m/Y') }}
                  @endif
                </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="p-4 text-center text-gray-500">
                    No hay movimientos registrados.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- Paginación --}}
        <div>
          {{ $historial->links() }}
        </div>

      </div>
    </main>
  </div>
</x-app-layout>

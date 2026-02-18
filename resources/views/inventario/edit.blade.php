{{-- resources/views/inventario/edit.blade.php --}}
<x-app-layout>
  <div class="flex">
    <x-sidebar />

    <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-6">
      <div class="max-w-lg mx-auto bg-white shadow rounded-lg p-6 space-y-6">

        <h1 class="text-2xl font-bold">Editar Inventario</h1>

        @if(session('success'))
          <div class="bg-green-100 text-green-800 px-4 py-2 rounded">
            {{ session('success') }}
          </div>
        @endif

        <form action="{{ route('inventario.update_stock', $inventario) }}" method="POST">
          @csrf

          {{-- Datos del producto (solo lectura) --}}
          <div class="grid grid-cols-1 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-600">Producto</label>
              <input type="text" disabled
                     value="{{ $inventario->nombreproducto }}"
                     class="w-full bg-gray-100 border-gray-300 rounded px-3 py-2" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-600">Área</label>
              <input type="text" disabled
                     value="{{ $inventario->area->nombre ?? '—' }}"
                     class="w-full bg-gray-100 border-gray-300 rounded px-3 py-2" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-600">Subárea</label>
              <input type="text" disabled
                     value="{{ $inventario->subarea->nombre ?? '—' }}"
                     class="w-full bg-gray-100 border-gray-300 rounded px-3 py-2" />
            </div>
            <div class="flex gap-4">
              <div class="flex-1">
                <label class="block text-sm font-medium text-gray-600">Cantidad actual</label>
                <input type="text" disabled
                       value="{{ $inventario->cantidad }}"
                       class="w-full bg-gray-100 border-gray-300 rounded px-3 py-2 text-center" />
              </div>
              <div class="flex-1">
                <label class="block text-sm font-medium text-gray-600">Stock mínimo</label>
                <input type="text" disabled
                       value="{{ $inventario->stockminimo }}"
                       class="w-full bg-gray-100 border-gray-300 rounded px-3 py-2 text-center" />
              </div>
            </div>
          </div>

          {{-- Ajuste de stock --}}
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-600">
                @role('admin')
                  Agregar/Restar cantidad
                @else
                  Restar cantidad
                @endrole
              </label>
              <input name="cantidad_operacion" type="number" min="0" step="1"
                     class="w-full border-gray-300 rounded px-3 py-2"
                     placeholder="Cantidad a ajustar…" required>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-600">Tipo de operación</label>
              <select name="tipo_operacion"
                      class="w-full border-gray-300 rounded px-3 py-2">
                @role('colaborador')
                  <option value="salida" selected>Extraer</option>
                  <option value="entrada">Agregar</option>
                @else
                  <option value="entrada" selected>Agregar</option>
                  <option value="salida">Extraer</option>
                @endrole
              </select>
            </div>
          </div>

          {{-- Botones --}}
          <div class="flex justify-between items-center mt-6">
            <a href="{{ route('inventario.index') }}"
               class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded">
              ← Volver
            </a>
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
              Guardar cambios
            </button>
          </div>
        </form>

      </div>
    </main>
  </div>
</x-app-layout>

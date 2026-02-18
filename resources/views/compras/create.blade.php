{{-- resources/views/compras/create.blade.php --}}
<x-app-layout>
  <div class="flex">
    <x-sidebar />

    <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-6">
      <div class="max-w-3xl mx-auto bg-white shadow rounded-xl p-8 space-y-8">

        <h1 class="text-2xl font-bold text-sky-700 flex items-center gap-2">
          <i class="fa-solid fa-cart-plus"></i> Registrar nueva compra
        </h1>

        {{-- Errores de validación --}}
        @if($errors->any())
          <div class="bg-red-100 border border-red-300 text-red-800 p-4 rounded mb-4">
            <ul class="list-disc pl-5">
              @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form action="{{ route('compras.store') }}" method="POST" class="space-y-6">
          @csrf

          {{-- Área --}}
          <div>
            <label class="block mb-1 font-semibold">Área del producto</label>
            <select name="area_id"
                    class="w-full border rounded px-3 py-2"
                    required>
              <option value="">— Seleccionar área —</option>
              @foreach($areas as $id => $nombre)
                <option value="{{ $id }}"
                        @selected(old('area_id', $defaultAreaId) == $id)>
                  {{ $nombre }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Proveedor --}}
            @role('admin|editor')
            <div>
                <label class="block mb-1 font-semibold">Proveedor</label>
                <select name="proveedor_id"
                        class="w-full border rounded px-3 py-2"
                        required>
                <option value="">— Seleccionar proveedor —</option>
                @foreach($proveedores as $id => $nombre)
                    <option value="{{ $id }}" @selected(old('proveedor_id') == $id)>
                    {{ $nombre }}
                    </option>
                @endforeach
                </select>
            </div>
            @else
            {{-- Para colaboradores el proveedor se guarda como NULL --}}
            <input type="hidden" name="proveedor_id" value="">
            @endrole

          {{-- Prioridad + Estado --}}
          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label class="block mb-1 font-semibold">Prioridad</label>
              <select name="prioridad"
                      class="w-full border rounded px-3 py-2"
                      required>
                @foreach($prioridades as $p)
                  <option value="{{ $p }}" @selected(old('prioridad','Media')==$p)>
                    {{ $p }}
                  </option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="block mb-1 font-semibold">Estado</label>
              <select name="estado"
                      class="w-full border rounded px-3 py-2"
                      required>
                @foreach($estados as $e)
                  <option value="{{ $e }}" @selected(old('estado','Compra a realizar')==$e)>
                    {{ $e }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>

          {{-- Producto & Cantidad --}}
          <div>
            <label class="block mb-1 font-semibold">Producto a comprar</label>
            <textarea name="producto"
                      rows="3"
                      class="w-full border rounded px-3 py-2"
                      required>{{ old('producto', $defaultProducto) }}</textarea>
          </div>

          <div>
            <label class="block mb-1 font-semibold">Cantidad</label>
            <input type="number"
                   name="cantidad"
                   min="1"
                   class="w-full border rounded px-3 py-2"
                   value="{{ old('cantidad', 1) }}"
                   required>
          </div>

          {{-- Responsable --}}
          <div>
            <label class="block mb-1 font-semibold">Responsable de la compra</label>
            <input type="text"
                   name="responsable"
                   value="{{ old('responsable', Auth::user()->name) }}"
                   class="w-full border rounded px-3 py-2"
                   required>
          </div>

          <div class="flex justify-end gap-4">
            <a href="{{ route('compras.index') }}"
               class="px-4 py-2 bg-slate-500 text-white rounded hover:bg-slate-600">
              Cancelar
            </a>
            <button type="submit"
                    class="px-5 py-2 bg-sky-600 text-white rounded hover:bg-sky-700">
              Guardar compra
            </button>
          </div>
        </form>

      </div>
    </main>
  </div>
</x-app-layout>

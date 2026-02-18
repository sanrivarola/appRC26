<x-app-layout>
  <div class="flex">
    <x-sidebar />

    <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-6">
      <div class="max-w-3xl mx-auto bg-white shadow rounded-xl p-8 space-y-8">

        {{-- CABECERA --}}
        <h1 class="text-2xl font-bold text-sky-700 flex items-center gap-2">
          <i class="fa-solid fa-cart-shopping"></i> Editar compra
        </h1>

        {{-- ERRORES DE VALIDACIÓN --}}
        @if($errors->any())
          <div class="bg-red-100 border border-red-300 text-red-800 p-4 rounded">
            <ul class="list-disc pl-5">
              @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        {{-- FORMULARIO --}}
        <form action="{{ route('compras.update', $compra->id_compras) }}"
              method="POST" class="space-y-6">
          @csrf
          @method('PUT')

          {{-- ÁREA ---------------------------------------------------------- --}}
          <div>
            <label class="block mb-1 font-semibold">Área del producto</label>
            <select name="area_id"
                    class="w-full border rounded px-3 py-2"
                    required>
              <option value="">— Seleccionar área —</option>
              @foreach($areas as $id => $nombre)
                <option value="{{ $id }}"
                        @selected(old('area_id', $compra->area_id)==$id)>
                  {{ $nombre }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- PROVEEDOR (solo admin / editor) ------------------------------ --}}
          @role('admin|editor')
            <div>
              <label class="block mb-1 font-semibold">Proveedor</label>
              <select name="proveedor_id"
                      class="w-full border rounded px-3 py-2"
                      required>
                <option value="">— Seleccionar proveedor —</option>
                @foreach($proveedores as $id => $nombre)
                  <option value="{{ $id }}"
                          @selected(old('proveedor_id',$compra->proveedor_id)==$id)>
                    {{ $nombre }}
                  </option>
                @endforeach
              </select>
            </div>
          @else
            {{-- Colaborador: solo muestra el nombre si ya existe --}}
            <div>
              <label class="block mb-1 font-semibold">Proveedor</label>
              <input type="text"
                     value="{{ $compra->proveedor->nombre ?? '— sin asignar —' }}"
                     class="w-full border rounded px-3 py-2 bg-gray-100"
                     disabled>
              {{-- mantiene el valor para el update --}}
              <input type="hidden" name="proveedor_id"
                     value="{{ $compra->proveedor_id }}">
            </div>
          @endrole

          {{-- PRIORIDAD & ESTADO  ----------------------------------------- --}}
          <div class="grid md:grid-cols-2 gap-6">
            {{-- Prioridad --}}
            <div>
              <label class="block mb-1 font-semibold">Prioridad</label>
              <select name="prioridad"
                      class="w-full border rounded px-3 py-2"
                      required>
                @foreach($prioridades as $p)
                  <option value="{{ $p }}"
                          @selected(old('prioridad',$compra->prioridad)===$p)>
                    {{ $p }}
                  </option>
                @endforeach
              </select>
            </div>

            {{-- Estado --}}
            <div>
              <label class="block mb-1 font-semibold">Estado</label>
              <select name="estado"
                      class="w-full border rounded px-3 py-2"
                      required>
                @foreach($estados as $e)
                  <option value="{{ $e }}"
                          @selected(old('estado',$compra->estado)===$e)>
                    {{ $e }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>

          {{-- PRODUCTO & CANTIDAD ----------------------------------------- --}}
          <div>
            <label class="block mb-1 font-semibold">Producto</label>
            <textarea name="producto" rows="3"
                      class="w-full border rounded px-3 py-2"
                      required>{{ old('producto',$compra->producto) }}</textarea>
          </div>

          <div>
            <label class="block mb-1 font-semibold">Cantidad</label>
            <input type="number" min="1"
                   name="cantidad"
                   value="{{ old('cantidad',$compra->cantidad) }}"
                   class="w-full border rounded px-3 py-2"
                   required>
          </div>

          {{-- RESPONSABLE -------------------------------------------------- --}}
          <div>
            <label class="block mb-1 font-semibold">Responsable de la compra</label>
            <input type="text"
                   name="responsable"
                   value="{{ old('responsable',$compra->responsable) }}"
                   class="w-full border rounded px-3 py-2"
                   required>
          </div>
          
          <input type="hidden" name="fechacarga"
       value="{{ old('fechacarga', optional($compra->fechacarga)->format('Y-m-d')) }}">

          {{-- BOTONES ------------------------------------------------------ --}}
          <div class="flex justify-end gap-4">
            <a href="{{ url()->previous() }}"
               class="px-4 py-2 bg-slate-500 text-white rounded hover:bg-slate-600">
              Cancelar
            </a>
            <button type="submit"
                    class="px-5 py-2 bg-sky-600 text-white rounded hover:bg-sky-700">
              Actualizar
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>
</x-app-layout>

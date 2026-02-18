<x-app-layout>
  <div class="flex">
    <x-sidebar />

    <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-6">
      <div class="max-w-lg mx-auto bg-white shadow rounded-lg p-6">

        <h1 class="text-xl font-bold mb-4">Nuevo mantenimiento</h1>

        @if($errors->any())
          <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
              @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form action="{{ route('mantenimientos.store') }}" method="POST">
          @csrf

          {{-- Tipo --}}
          <label class="block mb-2">Tipo de mantenimiento</label>
          <input name="tipo" value="{{ old('tipo') }}"
                 class="w-full border rounded px-3 py-2 mb-4" required>

          {{-- Responsable --}}
          <label class="block mb-2">Responsable - Empresa o Persona de Servicio</label>
          <input name="responsable" value="{{ old('responsable') }}"
                 class="w-full border rounded px-3 py-2 mb-4">

          {{-- Frecuencia: valor + unidad --}}
          <label class="block mb-2">Frecuencia del mantenimiento</label>
          <div class="flex gap-2 mb-4">
            <input
              type="number" name="frequency_value"
              min="1" value="{{ old('frequency_value') }}"
              class="w-1/3 border rounded px-2 py-1"
              placeholder="">
            <select name="frequency_unit"
                    class="w-2/3 border rounded px-2 py-1" required>
              <option value="días"     @selected(old('frequency_unit')==='días')>Días</option>
              <option value="semanas"  @selected(old('frequency_unit')==='semanas')>Semanas</option>
              <option value="meses"    @selected(old('frequency_unit')==='meses')>Meses</option>
              <option value="años"     @selected(old('frequency_unit')==='años')>Años</option>
              <option value="única vez" @selected(old('frequency_unit')==='única vez')>Única vez</option>
            </select>
          </div>

          {{-- Tiempo (se rellenará con JS automáticamente) --}}
          <input type="hidden" id="tiempo" name="tiempo" value="{{ old('tiempo') }}">



          <label class="block mb-2">Descripción</label>
          <textarea name="descripcion"
                    class="w-full border rounded px-3 py-2 mb-4"
                    rows="4">{{ old('descripcion',$m->descripcion ?? '') }}</textarea>

          {{-- Equipo --}}
          <label class="block mb-2">Persona a Cargo</label>
          <select name="equipo" class="w-full border rounded px-3 py-2 mb-4" required>
            <option value="">Seleccionar</option>
            @foreach($users as $name)
              <option value="{{ $name }}" @selected(old('equipo')===$name)>{{ $name }}</option>
            @endforeach
          </select>

          {{-- Estado: siempre activo al crear --}}
          <input type="hidden" name="estado" value="1">

          <button type="submit"
                  class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
            Guardar
          </button>
        </form>
      </div>
    </main>
  </div>

  <script>
    // Clona el mismo script de edit para construir 'tiempo'
    document.addEventListener('DOMContentLoaded',()=>{
      const val     = document.querySelector('input[name="frequency_value"]');
      const unit    = document.querySelector('select[name="frequency_unit"]');
      const tiempo  = document.getElementById('tiempo');

      function updateTiempo() {
        if (unit.value==='única vez') {
          val.value = '';
          val.disabled = true;
          tiempo.value = 'única vez';
        } else {
          val.disabled = false;
          tiempo.value = val.value + ' ' + unit.value;
        }
      }

      val.addEventListener('input', updateTiempo);
      unit.addEventListener('change', updateTiempo);
      updateTiempo();
    });
  </script>
</x-app-layout>

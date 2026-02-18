{{-- resources/views/mantenimientos/edit.blade.php --}}
<x-app-layout>
  <div class="flex">
    <x-sidebar />

    <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-6">
      <div class="max-w-lg mx-auto bg-white shadow rounded-lg p-6">

        <h1 class="text-xl font-bold mb-4">
          Editar mantenimiento
        </h1>

        @if($errors->any())
          <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
              @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form
          action="{{ route('mantenimientos.update', $m->id_mantenimiento) }}"
          method="POST"
        >
          @csrf
          @method('PUT')

          {{-- Tipo --}}
          <label class="block mb-2">Tipo de mantenimiento</label>
          <input name="tipo"
                 value="{{ old('tipo', $m->tipo) }}"
                 class="w-full border rounded px-3 py-2 mb-4"
                 required>

          {{-- Responsable --}}
          <label class="block mb-2">Empresa para este servicio / producto</label>
          <input name="responsable"
                 value="{{ old('responsable', $m->responsable) }}"
                 class="w-full border rounded px-3 py-2 mb-4">

          {{-- Frecuencia de aviso --}}
          <label class="block mb-2">Frecuencia del mantenimiento</label>
          <div class="flex gap-2 mb-4">
            <input
              type="number"
              id="frequencyValue"
              name="frequency_value"
              value="{{ old('frequency_value', $frequencyValue ?? '') }}"
              min="1"
              class="w-1/3 border rounded px-2 py-1"
              @if(old('frequency_unit', $frequencyUnit ?? '')==='única vez') disabled @endif
            >

            <select id="frequencyUnit"
                    name="frequency_unit"
                    class="w-2/3 border rounded px-2 py-1">
              <option value="días"     @selected(old('frequency_unit',$frequencyUnit ?? '')==='días')>Días</option>
              <option value="semanas"  @selected(old('frequency_unit',$frequencyUnit ?? '')==='semanas')>Semanas</option>
              <option value="meses"    @selected(old('frequency_unit',$frequencyUnit ?? '')==='meses')>Meses</option>
              <option value="años"     @selected(old('frequency_unit',$frequencyUnit ?? '')==='años')>Años</option>
              <option value="única vez" @selected(old('frequency_unit',$frequencyUnit ?? '')==='única vez')>Única vez</option>
            </select>
          </div>

          <input type="hidden" id="tiempo" name="tiempo"
                 value="{{ old('tiempo', $m->tiempo ?? '') }}">

          {{-- Mostrar próxima fecha calculada --}}
          @if($m->next_due_at)
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700">
                Próxima fecha programada
              </label>
              <input
                type="date"
                name="next_due_at"
                class="w-full border rounded px-3 py-2"
                readonly
                value="{{ old('next_due_at', $m->next_due_at) }}"
              >
            </div>
          @endif

          {{-- Descripción --}}
          <label class="block mb-2">Descripción</label>
          <textarea name="descripcion"
                    class="w-full border rounded px-3 py-2 mb-4"
                    rows="4">{{ old('descripcion', $m->descripcion) }}</textarea>

          {{-- Equipo (select de usuarios activos) --}}
          <label class="block mb-2">Responsable - Empresa o Persona de Servicio</label>
          <select name="equipo"
                  class="w-full border rounded px-3 py-2 mb-6"
                  required>
            <option value="">Seleccionar</option>
            @foreach($users as $name)
              <option value="{{ $name }}"
                @selected(old('equipo', $m->equipo) === $name)>
                {{ $name }}
              </option>
            @endforeach
          </select>

          @role('admin')
            {{-- Estado --}}
            <label class="block mb-2">Estado</label>
            <select name="estado"
                    class="w-full border rounded px-3 py-2 mb-4"
                    required>
              <option value="1" @selected(old('estado', $m->estado)==1)>Activo</option>
              <option value="0" @selected(old('estado', $m->estado)==0)>Archivado</option>
            </select>
          @else
            {{-- Colaborador nunca edita estado: siempre activo --}}
            <input type="hidden" name="estado" value="1">
          @endrole

          <button type="submit"
                  class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
            Actualizar
          </button>
        </form>
      </div>
    </main>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded',()=>{
      const val     = document.getElementById('frequencyValue');
      const unit    = document.getElementById('frequencyUnit');
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

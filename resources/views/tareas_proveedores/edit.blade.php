<x-app-layout>
<div class="flex">
  <x-sidebar />
  <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-6 md:p-10">
    <div class="max-w-2xl mx-auto">

      <div class="bg-white rounded-2xl shadow p-8 space-y-6">
        <div class="flex items-center justify-between">
          <h1 class="text-xl font-bold text-sky-700">Editar tarea</h1>
          <a href="{{ route('tareas-proveedores.show', $tarea) }}" class="text-slate-400 hover:text-slate-600 text-sm">← Volver</a>
        </div>

        @if($errors->any())
          <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm space-y-1">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
          </div>
        @endif

        <form method="POST" action="{{ route('tareas-proveedores.update', $tarea) }}" class="space-y-5">
          @csrf
          @method('PUT')

          {{-- Proveedor (solo lectura en edición) --}}
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Proveedor</label>
            <div class="border rounded-lg px-3 py-2 text-sm bg-slate-50 text-slate-600">
              {{ $tarea->proveedor->nombre ?? '—' }}
            </div>
          </div>

          {{-- Título --}}
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Título <span class="text-red-500">*</span></label>
            <input name="titulo" value="{{ old('titulo', $tarea->titulo) }}" required
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-sky-400 focus:border-sky-400">
          </div>

          {{-- Tipo + Prioridad + Estado --}}
          <div class="grid grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Tipo</label>
              <select name="tipo" class="w-full border rounded-lg px-3 py-2 text-sm">
                @foreach(['Reclamo','Consulta','Seguimiento','Renovación'] as $t)
                  <option value="{{ $t }}" @selected(old('tipo',$tarea->tipo) === $t)>{{ $t }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Prioridad</label>
              <select name="prioridad" class="w-full border rounded-lg px-3 py-2 text-sm">
                @foreach(['Alta','Media','Baja'] as $p)
                  <option value="{{ $p }}" @selected(old('prioridad',$tarea->prioridad) === $p)>{{ $p }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Estado</label>
              <select name="estado" class="w-full border rounded-lg px-3 py-2 text-sm">
                @foreach(['Pendiente','En curso','Cerrada'] as $e)
                  <option value="{{ $e }}" @selected(old('estado',$tarea->estado) === $e)>{{ $e }}</option>
                @endforeach
              </select>
            </div>
          </div>

          {{-- Fecha límite --}}
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Fecha límite</label>
            <input type="date" name="fecha_limite" value="{{ old('fecha_limite', optional($tarea->fecha_limite)->format('Y-m-d')) }}"
                   class="border rounded-lg px-3 py-2 text-sm focus:ring-sky-400 focus:border-sky-400">
          </div>

          {{-- Detalle --}}
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Detalle</label>
            <textarea name="detalle" rows="5"
                      class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-sky-400 focus:border-sky-400 resize-none">{{ old('detalle', $tarea->detalle) }}</textarea>
          </div>

          <div class="flex items-center justify-between pt-2">
            {{-- Eliminar --}}
            <form method="POST" action="{{ route('tareas-proveedores.destroy', $tarea) }}"
                  onsubmit="return confirm('¿Eliminar esta tarea y todo su historial?')">
              @csrf @method('DELETE')
              <button type="submit" class="text-sm text-red-500 hover:text-red-700 hover:underline">
                Eliminar tarea
              </button>
            </form>

            <div class="flex gap-3">
              <a href="{{ route('tareas-proveedores.show', $tarea) }}"
                 class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition text-sm font-semibold">
                Cancelar
              </a>
              <button type="submit"
                      class="px-6 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition text-sm font-semibold">
                Guardar cambios
              </button>
            </div>
          </div>
        </form>
      </div>

    </div>
  </main>
</div>
</x-app-layout>

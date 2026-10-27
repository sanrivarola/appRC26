<x-app-layout>
<div class="flex">
  <x-sidebar />
  <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-6 md:p-10">
    <div class="max-w-2xl mx-auto">

      <div class="bg-white rounded-2xl shadow p-8 space-y-6">
        <div class="flex items-center justify-between">
          <h1 class="text-xl font-bold text-sky-700">Nueva tarea de proveedor</h1>
          <a href="{{ route('tareas-proveedores.index') }}" class="text-slate-400 hover:text-slate-600 text-sm">← Volver</a>
        </div>

        @if($errors->any())
          <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm space-y-1">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
          </div>
        @endif

        <form method="POST" action="{{ route('tareas-proveedores.store') }}" class="space-y-5">
          @csrf

          {{-- Proveedor --}}
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Proveedor <span class="text-red-500">*</span></label>
            <select name="proveedor_id" required
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-sky-400 focus:border-sky-400">
              <option value="">Seleccioná un proveedor</option>
              @foreach($proveedores as $id => $nombre)
                <option value="{{ $id }}" @selected(old('proveedor_id', $preselect) == $id)>{{ $nombre }}</option>
              @endforeach
            </select>
          </div>

          {{-- Título --}}
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Título <span class="text-red-500">*</span></label>
            <input name="titulo" value="{{ old('titulo') }}" required
                   placeholder="Ej: Llamar para reclamar mal servicio de internet"
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-sky-400 focus:border-sky-400">
          </div>

          {{-- Tipo + Prioridad --}}
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Tipo <span class="text-red-500">*</span></label>
              <select name="tipo" required class="w-full border rounded-lg px-3 py-2 text-sm">
                @foreach(['Reclamo','Consulta','Seguimiento','Renovación'] as $t)
                  <option value="{{ $t }}" @selected(old('tipo') === $t)>{{ $t }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Prioridad <span class="text-red-500">*</span></label>
              <select name="prioridad" required class="w-full border rounded-lg px-3 py-2 text-sm">
                @foreach(['Alta','Media','Baja'] as $p)
                  <option value="{{ $p }}" @selected(old('prioridad','Media') === $p)>{{ $p }}</option>
                @endforeach
              </select>
            </div>
          </div>

          {{-- Fecha límite --}}
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Fecha límite</label>
            <input type="date" name="fecha_limite" value="{{ old('fecha_limite') }}"
                   class="border rounded-lg px-3 py-2 text-sm focus:ring-sky-400 focus:border-sky-400">
          </div>

          {{-- Detalle --}}
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Detalle</label>
            <textarea name="detalle" rows="4"
                      placeholder="Describí lo que hay que hacer, contexto, datos relevantes…"
                      class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-sky-400 focus:border-sky-400 resize-none">{{ old('detalle') }}</textarea>
          </div>

          <div class="flex gap-3 justify-end pt-2">
            <a href="{{ route('tareas-proveedores.index') }}"
               class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition text-sm font-semibold">
              Cancelar
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition text-sm font-semibold">
              Crear tarea
            </button>
          </div>
        </form>
      </div>

    </div>
  </main>
</div>
</x-app-layout>

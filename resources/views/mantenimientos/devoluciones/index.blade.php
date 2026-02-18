<x-app-layout>
  <x-sidebar />
  <main class="...">
    <div class="max-w-4xl mx-auto space-y-6">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold">Historial de devoluciones</h1>
        <a href="{{ route('mantenimientos.show',$m->id_mantenimiento) }}"
           class="text-gray-600 hover:underline">← Volver</a>
      </div>

      {{-- Buscador por fecha --}}
      <form class="flex gap-2 mb-6">
        <input type="date" name="fecha" value="{{ $fecha }}"
               class="border rounded px-3 py-1" />
        <button class="bg-gray-200 px-3 py-1 rounded">Filtrar</button>
      </form>

      <ul class="space-y-4">
        @forelse($devs as $dev)
          <li class="border-l-2 pl-4">
            <div class="flex justify-between">
              <span class="class="text-xs text-gray-500">{{ $dev->equipo }}</span>
              <time class="text-xs text-gray-500">
                {{ \Carbon\Carbon::parse($dev->fecha)->format('d/m/Y') }}
              </time>
            </div>
            <p class="mt-1 font-medium">{{ $dev->devolucion }}</p>
          </li>
        @empty
          <li class="text-gray-500">No hay devoluciones para esta búsqueda.</li>
        @endforelse
      </ul>

      {{-- Paginación --}}
      <div class="mt-4">{{ $devs->links() }}</div>
    </div>
  </main>
</x-app-layout>

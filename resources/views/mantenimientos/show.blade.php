<x-app-layout>
  <div class="flex">
    <x-sidebar />

    <main class="flex-1 bg-slate-100 min-h-screen md:ml-64 p-6 md:p-10">
      <div class="max-w-4xl mx-auto backdrop-blur-md bg-white/80 shadow-xl rounded-3xl p-8">

        {{-- ENCABEZADO --}}
        <div class="flex items-center justify-between mb-6">
          <h1 class="text-3xl font-extrabold text-emerald-700 flex items-center gap-3">
            {{ $mantenimiento->tipo }}
            <span @class([
              'ml-4 inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full',
              'bg-red-200 text-red-700'     => $mantenimiento->estado == 0,
              'bg-green-200 text-green-700' => $mantenimiento->estado == 1,
            ])>
              @if($mantenimiento->estado == 1)
                <i class="fa-solid fa-circle-check"></i> Activo
              @else
                <i class="fa-solid fa-archive"></i> Archivado
              @endif
            </span>
          </h1>
          <a href="{{ route('mantenimientos.index') }}"
             class="inline-flex items-center gap-2 px-4 py-2 bg-slate-500 text-white rounded hover:bg-slate-600 transition">
            <i class="fa-solid fa-arrow-left"></i> Volver
          </a>
        </div>

        {{-- DESCRIPCIÓN --}}
        @if($mantenimiento->descripcion)
          <div class="prose max-w-none mb-8 border border-gray-300 bg-gray-100 p-4 rounded-lg">
            {!! nl2br(e($mantenimiento->descripcion)) !!}
          </div>
        @endif

        {{-- GRID INFO RÁPIDA --}}
        <div class="grid gap-4 md:grid-cols-2 mb-8">
          <x-info-box label="Fecha de carga">
            {{ \Carbon\Carbon::parse($mantenimiento->fechacarga)->format('d/m/Y') }}
          </x-info-box>
          <x-info-box label="Responsable">
            {{ $mantenimiento->responsable ?? '—' }}
          </x-info-box>
          <x-info-box label="Frecuencia">
            {{ $mantenimiento->tiempo }}
          </x-info-box>
          <x-info-box label="Equipo a cargo">
            {{ $mantenimiento->equipo }}
          </x-info-box>
          @if($mantenimiento->next_due_at)
            <x-info-box label="Próximo mantenimiento">
              {{ \Carbon\Carbon::parse($mantenimiento->next_due_at)->format('d/m/Y') }}
            </x-info-box>
          @endif
          @if($mantenimiento->motivo_archivo)
            <x-info-box label="Motivo de archivo">
              {{ $mantenimiento->motivo_archivo }}
            </x-info-box>
          @endif
        </div>

        {{-- FORMULARIO DE DEVOLUCIÓN (solo colaborador) --}}
        @role('colaborador')
          <form action="{{ route('mantenimientos.devoluciones.store', $mantenimiento->id_mantenimiento) }}"
                method="POST" class="space-y-4 bg-white p-6 rounded-lg shadow">
            @csrf
            <h2 class="text-lg font-semibold text-teal-700 flex items-center gap-2">
              <i class="fa-solid fa-paper-plane"></i> Registrar devolución
            </h2>
            <textarea name="devolucion"
                      rows="3"
                      class="w-full border rounded p-2"
                      placeholder="Comentario…"></textarea>
            <button type="submit"
                    class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded">
              Guardar
            </button>
          </form>
        @endrole

        {{-- ÚLTIMAS 10 DEVOLUCIONES --}}
        <h2 class="text-lg font-semibold mt-6 flex items-center gap-2">
          <i class="fa-solid fa-list"></i> Últimas devoluciones
        </h2>
        <ul class="space-y-4">
          @forelse($recientes as $dev)
            <li class="border-l-2 pl-4">
              <div class="flex justify-between">
                <time class="text-xs text-gray-500">
                  {{ \Carbon\Carbon::parse($dev->fecha)->format('d/m/Y') }}
                </time>
              </div>
              <p class="text-sm font-medium">{{ $dev->devolucion }}</p>
            </li>
          @empty
            <li class="text-gray-500">Aún no hay devoluciones.</li>
          @endforelse
        </ul>

        {{-- Enlace a historial completo para admin --}}
        @role('admin')
          <a href="{{ route('mantenimientos.devoluciones.index', $mantenimiento->id_mantenimiento) }}"
             class="inline-block mt-4 text-indigo-600 hover:underline">
            Ver historial completo…
          </a>
        @endrole

      </div>
    </main>
  </div>
</x-app-layout>

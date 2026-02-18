{{-- resources/views/inventario/show.blade.php --}}
<x-app-layout>
  <div class="flex">
    <x-sidebar />

    <main class="flex-1 bg-slate-100 min-h-screen md:ml-64 p-6 md:p-10">
      <div class="max-w-4xl mx-auto backdrop-blur-md bg-white/80 shadow-xl rounded-3xl p-8">

        {{-- Cabecera con título y Volver --}}
        <div class="flex items-center justify-between mb-6">
          <h1 class="text-3xl font-extrabold text-green-700 flex items-center gap-2">
            <svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z' />
            </svg>
            {{ $inventario->nombreproducto }}
          </h1>
          <a href="{{ route('inventario.index') }}"
             class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
            <i class="fa-solid fa-arrow-left"></i> Volver
          </a>
        </div>

        {{-- Detalles --}}
        <div class="bg-white shadow rounded-2xl p-6 space-y-6">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
              <h3 class="text-sm font-semibold text-gray-500">Área</h3>
              <p class="mt-1 text-gray-800">{{ $inventario->area->nombre ?? '—' }}</p>
            </div>
            <div>
              <h3 class="text-sm font-semibold text-gray-500">Subárea</h3>
              <p class="mt-1 text-gray-800">{{ $inventario->subarea->nombre ?? '—' }}</p>
            </div>
            <div>
              <h3 class="text-sm font-semibold text-gray-500">Stock actual</h3>
              <p class="mt-1 text-gray-800">{{ $inventario->cantidad }}</p>
            </div>
            <div>
              <h3 class="text-sm font-semibold text-gray-500">Stock mínimo</h3>
              <p class="mt-1 text-gray-800">{{ $inventario->stockminimo }}</p>
            </div>
            @if($inventario->vencimiento)
              <div>
                <h3 class="text-sm font-semibold text-gray-500">Vencimiento</h3>
                <p class="mt-1 text-gray-800">
                  {{ \Carbon\Carbon::parse($inventario->vencimiento)->format('d/m/Y') }}
                </p>
              </div>
            @endif
            <div>
              <h3 class="text-sm font-semibold text-gray-500">Fecha de carga</h3>
              <p class="mt-1 text-gray-800">
                {{ \Carbon\Carbon::parse($inventario->fechacarga)->format('d/m/Y') }}
              </p>
            </div>
          </div>

          @if($inventario->detalle)
            <div class="prose max-w-none border border-gray-300 bg-gray-100 p-4 rounded-lg">
              <h3 class="text-sm font-semibold text-gray-500">Detalle</h3>
              <p class="mt-1 whitespace-pre-line">{{ $inventario->detalle }}</p>
            </div>
          @endif
        </div>

      </div>
    </main>
  </div>
</x-app-layout>

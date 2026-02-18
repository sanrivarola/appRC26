{{-- resources/views/mantenimientos/archivados.blade.php --}}
<x-app-layout>
  <div class="flex">
    <x-sidebar />

    <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-4 md:p-8 transition-all">
      <div class="max-w-6xl mx-auto space-y-6">

        {{-- Cabecera e incluso botón “Volver” o “Ver activos” --}}
        <div class="bg-white shadow rounded-2xl p-6 flex items-center justify-between">
          <h2 class="text-2xl font-semibold text-red-700">
            Mantenimientos Archivados
          </h2>
          <a href="{{ route('mantenimientos.index') }}"
             class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 text-gray-800 rounded">
            ← Ver Activos
          </a>
        </div>

        {{-- Incluir la tabla --}}
        @include('mantenimientos._tabla', ['routePrefix' => 'mantenimientos.archivados'])

      </div>
    </main>
  </div>
</x-app-layout>

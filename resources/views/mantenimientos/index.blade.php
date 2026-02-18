{{-- resources/views/mantenimientos/index.blade.php --}}
<x-app-layout>
  <div class="flex">
    <x-sidebar />

    <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-4 md:p-8 transition-all">
      <div class="max-w-6xl mx-auto space-y-6">

        {{-- Encabezado con Nuevo + Search + Mostrar --}}
        <div class="bg-white shadow rounded-2xl p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <h2 class="text-2xl font-semibold text-emerald-700 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                 class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 
                       2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192 
                       a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21
                       -.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 
                       .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 
                       0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 
                       1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 
                       8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125
                       .504-1.125 1.125v11.25c0 .621.504 1.125 
                       1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375
                       c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008
                       H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
            </svg>
            Mantenimientos
          </h2>

          <div class="flex flex-wrap gap-3 items-end">

            {{-- + Nuevo mantenimiento (admin o colaborador) --}}
            <a href="{{ route('mantenimientos.create') }}"
              class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-600 
                      text-white px-4 py-2 rounded font-semibold transition">
              + Nuevo mantenimiento
            </a>

            @role('admin')
              <a href="{{ route('mantenimientos.archivados') }}"
                class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded font-semibold">
                Archivados
              </a>
            @endrole

            {{-- Search form --}}
            <form method="GET" action="{{ route('mantenimientos.index') }}" class="flex items-center gap-2">
              @foreach(request()->except(['search','perPage','page']) as $key => $val)
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
              @endforeach
              <input type="text" name="search" placeholder="Buscar…" value="{{ request('search') }}"
                    class="border rounded px-3 py-2">
              <button type="submit"
                      class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded">
                Buscar
              </button>
            </form>

            {{-- Mostrar / página --}}
            <form>
              @foreach(request()->except(['perPage','page']) as $key => $val)
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
              @endforeach
              <label for="perPage" class="text-sm text-gray-600 mr-1">Mostrar:</label>
              <select name="perPage" id="perPage"
                      class="border rounded px-8 py-1"
                      onchange="this.form.submit()">
                <option value="25" @selected($perPage==25)>25</option>
                <option value="50" @selected($perPage==50)>50</option>
                <option value="100" @selected($perPage==100)>100</option>
              </select>
            </form>

          </div>
        </div>

        {{-- Mensaje de éxito --}}
        @if(session('success'))
          <div class="bg-green-100 text-green-800 px-4 py-2 rounded shadow">
            {{ session('success') }}
          </div>
        @endif

        {{-- Tabla de Mantenimientos --}}
        <div class="bg-white shadow rounded-2xl p-6">
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-green-100 text-green-700">
                <tr>
                  <th class="p-2 font-bold text-left">Fecha</th>
                  <th class="p-2 font-bold text-left">Tipo</th>
                  <th class="p-2 font-bold text-left">Frecuencia</th>
                  <th class="p-2 font-bold text-left">Estado</th>
                  <th class="p-2 font-bold text-left">Equipo</th>
                  <th class="p-2 font-bold text-center">Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse($mantenciones as $m)
                      @php
                          $hoy = \Carbon\Carbon::today();
                          $next = $m->next_due_at ? \Carbon\Carbon::parse($m->next_due_at) : null;

                        if ($next && $next->lt($hoy)) {
                            $rowClass = 'bg-red-50'; // vencido
                        } elseif ($next && $next->lte($hoy->copy()->addDays(7))) {
                            $rowClass = 'bg-yellow-50'; // por vencer
                        } else {
                            $rowClass = '';
                        }
                      @endphp

                     <tr class="hover:bg-green-50 {{ $rowClass }}">
                    <td class="p-2">
                    {{ \Carbon\Carbon::parse($m->fechacarga)->format('d/m/Y') }}
                    @if($m->estaPorVencer())
                      <span class="ml-2 inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">
                        ¡Vence pronto!
                      </span>
                    @endif
                  </td>
                    <td class="p-2">
                      <a href="{{ route('mantenimientos.show', $m->id_mantenimiento) }}"
                         class="font-semibold text-emerald-700 hover:underline">
                        {{ $m->tipo }}
                      </a>
                    </td>
                    <td class="p-2">{{ $m->tiempo }}</td>
                    <td class="p-2">
                      @if($next && $next->lt($hoy))
                        <span class="text-red-600 font-semibold">Vencido</span>
                      @elseif($next && $next->lte($hoy->copy()->addDays(7)))
                        <span class="text-yellow-600 font-semibold">Por vencer</span>
                      @else
                        <span class="text-gray-500">OK</span>
                      @endif
                    </td>
                    <td class="p-2">{{ $m->equipo }}</td>
                    <td class="p-2 text-center space-x-2">
                      @role('admin')
                        <a href="{{ route('mantenimientos.edit',$m->id_mantenimiento) }}"
                          class="text-green-500 hover:text-green-700" title="Editar">
                          <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                      @unless($m->archivado)
                      {{-- <a href="{{ route('mantenimientos.archive.form', $m->id_mantenimiento) }}" --}}
                        <a href="#"
                           class="text-red-500 hover:underline" title="Archivar">
                          <i class="fa-solid fa-archive"></i>
                        </a>
                      @else
                        <span class="text-gray-400 text-xs">Archivado</span>
                      @endunless
                    @endrole
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6"
                        class="p-4 text-center text-gray-500">
                      No hay mantenimientos registrados.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          {{-- Paginación --}}
          <div class="mt-4">
            {{ $mantenciones->appends(request()->query())->links() }}
          </div>
        </div>

      </div>
    </main>
  </div>
</x-app-layout>

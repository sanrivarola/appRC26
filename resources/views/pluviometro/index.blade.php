{{-- resources/views/pluviometro/index.blade.php --}}
<x-app-layout>
  <div class="flex">
    <x-sidebar />

    <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-6">
      <div class="max-w-5xl mx-auto space-y-6">

        {{-- Cabecera --}}
        <div class="flex items-center justify-between">
          <h1 class="text-2xl font-semibold text-sky-700">
            <i class="fa-solid fa-cloud-showers-heavy"></i> Pluviómetro
          </h1>
          <a href="{{ route('pluviometro.create') }}"
             class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded">
            + Registrar lluvia
          </a>
        </div>

        {{-- Filtros (search + agrupación + perPage) --}}
        <form method="GET"
              class="flex flex-wrap gap-3 items-end bg-white shadow p-4 rounded">

          {{-- búsqueda --}}
          <input type="text" name="search"
                 value="{{ request('search') }}" placeholder="Buscar…"
                 class="border rounded px-3 py-2 flex-1">

          {{-- agrupación días/meses --}}
        <select name="group"
                class="border rounded w-32 px-3 py-2 text-left"
                onchange="this.form.submit()">
          <option value="dias"  @selected($agrupado==='dias')>Por días</option>
          <option value="meses" @selected($agrupado==='meses')>Por meses</option>
        </select>

          {{-- ítems por página --}}
          <select name="perPage" class="border w-32 rounded px-3 py-2"
                  onchange="this.form.submit()">
            @foreach([25,50,100] as $n)
              <option value="{{ $n }}" @selected($perPage==$n)>{{ $n }}</option>
            @endforeach
          </select>

          <button type="submit"
                  class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded">
            Aplicar
          </button>
        </form>

        {{-- Gráfico --}}
        <x-pluviometro-chart :datos="$datosGraf" :agrupado="$agrupado" />

        {{-- Tabla --}}
        <div class="bg-white shadow rounded-lg overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-sky-100 text-sky-700">
              <tr>
                <th class="p-2">Fecha</th>
                <th class="p-2">mm de lluvia</th>
                <th class="p-2">Responsable</th>
                <th class="p-2 text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
              @forelse($registros as $r)
                <tr class="hover:bg-sky-50">
                  <td class="p-2">{{ \Carbon\Carbon::parse($r->fechacarga)->format('d/m/Y') }}</td>
                  <td class="p-2">{{ $r->medicion }}</td>
                  <td class="p-2">{{ $r->equipo }}</td>
                  <td class="p-2 text-center space-x-2">
                    <a href="{{ route('pluviometro.edit', $r->id_pluviometro) }}"
                       class="text-green-600 hover:underline">Editar</a>
                    @role('admin')
                      <form action="{{ route('pluviometro.destroy', $r->id_pluviometro) }}"
                            method="POST" class="inline"
                            onsubmit="return confirm('¿Borrar registro?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:underline">Borrar</button>
                      </form>
                    @endrole
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="p-4 text-center text-gray-500">
                    Sin datos registrados.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- Paginación --}}
        <div>{{ $registros->links() }}</div>

      </div>
    </main>
  </div>
</x-app-layout>

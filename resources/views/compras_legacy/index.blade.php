{{-- resources/views/compras_legacy/index.blade.php --}}
<x-app-layout>
  <div class="flex">
    <x-sidebar />

    <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-8">
      <div class="max-w-6xl mx-auto space-y-10">

        {{-- ═════════════ CABECERA ═════════════ --}}
        <div class="flex items-center justify-between">
          <h1 class="text-2xl font-semibold text-emerald-700 flex items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left"></i>
            Compras antiguas
          </h1>

          <div class="flex gap-3">
            {{-- Volver a compras actuales --}}
            <a href="{{ route('compras.index') }}"
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded">
              Compras actuales
            </a>
          </div>
        </div>

        {{-- FLASH --}}
        @if(session('success'))
          <div class="bg-emerald-100 border border-emerald-300 text-emerald-800 px-4 py-2 rounded">
            {{ session('success') }}
          </div>
        @endif

        {{-- ═════════════ FILTROS ═════════════ --}}
        <form method="GET" class="flex flex-wrap gap-4 items-end bg-white p-4 rounded shadow">
          <div>
            <label class="block text-sm">Buscar</label>
            <input name="search" value="{{ request('search') }}"
                   class="border rounded px-3 py-2" placeholder="Producto…">
          </div>

          <div>
            <label class="block text-sm">Prioridad</label>
            <select name="prioridad" class="border rounded px-3 py-2"
                    onchange="this.form.submit()">
              <option value="">Todas</option>
              @foreach($prioridades as $p)
                <option value="{{ $p }}" @selected(request('prioridad')==$p)>
                  {{ $p }}
                </option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="block text-sm">Desde</label>
            <input type="date" name="desde" value="{{ request('desde') }}"
                   class="border rounded px-3 py-2">
          </div>

          <div>
            <label class="block text-sm">Hasta</label>
            <input type="date" name="hasta" value="{{ request('hasta') }}"
                   class="border rounded px-3 py-2">
          </div>

          <button class="ml-auto bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded">
            Filtrar
          </button>
        </form>

        {{-- ═════════════ TABLA ═════════════ --}}
        <section>
          <div class="overflow-x-auto bg-white rounded shadow">
            <table class="w-full border-collapse">
              <thead>
                <tr class="bg-slate-100 text-left text-sm font-medium text-slate-600">
                  <th class="px-4 py-2">ID</th>
                  <th class="px-4 py-2">Fecha carga</th>
                  <th class="px-4 py-2">Área</th>
                  <th class="px-4 py-2">Prioridad</th>
                  <th class="px-4 py-2">Producto</th>
                  <th class="px-4 py-2">Estado</th>
                  <th class="px-4 py-2">Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse($compras as $c)
                  <tr class="border-b hover:bg-slate-50">
                    <td class="px-4 py-2 text-sm">{{ $c->id_compras }}</td>
                    <td class="px-4 py-2 text-sm">{{ optional($c->fechacarga)->format('d/m/Y') }}</td>
                    <td class="px-4 py-2 text-sm">{{ $c->area->nombre ?? '—' }}</td>
                    <td class="px-4 py-2 text-sm">
                      <span @class([
                        'inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full',
                        'bg-red-200 text-red-700'      => $c->prioridad==='Alta',
                        'bg-yellow-200 text-yellow-700'=> $c->prioridad==='Media',
                        'bg-green-200 text-green-700'  => $c->prioridad==='Baja',
                      ])>
                        <i class="fa-solid fa-flag"></i> {{ $c->prioridad }}
                      </span>
                    </td>
                    <td class="px-4 py-2 text-sm">{{ Str::limit($c->producto, 40) }}</td>
                        @php
                          // Normalizo: minúsculas + trim + colapso de espacios
                          $estadoRaw  = (string) ($c->estado ?? '');
                          $estadoNorm = trim(mb_strtolower($estadoRaw, 'UTF-8'));
                          $estadoNorm = preg_replace('/\s+/u', ' ', $estadoNorm);
                        
                          // Verde SOLO si coincide exactamente con estos valores
                          $esRealizada = in_array($estadoNorm, ['compra realizada', 'realizada'], true);
                        
                          $badgeClass = $esRealizada
                            ? 'bg-green-200 text-green-700'
                            : 'bg-yellow-200 text-yellow-700';
                        @endphp
                        
                        <td class="px-4 py-2 text-sm">
                          <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full {{ $badgeClass }}">
                            <i class="fa-solid fa-circle"></i> {{ $c->estado }}
                          </span>
                        
                          {{-- Debug opcional: ?debug=1 --}}
                          @if(request()->boolean('debug'))
                            <div class="mt-1 text-[11px] text-slate-500">
                              raw:[{{ $estadoRaw }}] norm:[{{ $estadoNorm }}] => {{ $esRealizada ? 'VERDE' : 'AMARILLO' }}
                            </div>
                          @endif
                        </td>

                    <td class="px-4 py-2 text-sm">
                      <a href="{{ route('compras.legacy.show', $c->id_compras) }}"
                         class="text-sky-600 hover:underline">Ver</a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-slate-500">
                      No hay compras antiguas con los filtros aplicados.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          {{-- PAGINACIÓN --}}
          <div class="mt-4">
            {{ $compras->links() }}
          </div>
        </section>

      </div>
    </main>
  </div>
</x-app-layout>

<x-app-layout>
<div class="flex">
  <x-sidebar />
  <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-6 md:p-10">
    <div class="max-w-7xl mx-auto space-y-8">

      {{-- CABECERA --}}
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-sky-700 flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
          </svg>
          Tareas de Proveedores
        </h1>
        <a href="{{ route('tareas-proveedores.create') }}"
           class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg font-semibold flex items-center gap-2 transition">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
          Nueva tarea
        </a>
      </div>

      {{-- FLASH --}}
      @if(session('success'))
        <div class="bg-emerald-100 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-lg">
          {{ session('success') }}
        </div>
      @endif

      {{-- STATS --}}
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach([
          ['label'=>'Total','value'=>$stats['total'],    'color'=>'sky',    'icon'=>'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2'],
          ['label'=>'Pendientes','value'=>$stats['pendiente'],'color'=>'yellow','icon'=>'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
          ['label'=>'En curso','value'=>$stats['en_curso'], 'color'=>'blue',  'icon'=>'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99'],
          ['label'=>'Cerradas','value'=>$stats['cerradas'], 'color'=>'green', 'icon'=>'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
        ] as $s)
          <div class="bg-white rounded-xl border border-{{ $s['color'] }}-200 p-4 flex items-center gap-4">
            <div class="bg-{{ $s['color'] }}-100 p-2 rounded-lg">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-{{ $s['color'] }}-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $s['icon'] }}"/>
              </svg>
            </div>
            <div>
              <div class="text-2xl font-bold text-slate-800">{{ $s['value'] }}</div>
              <div class="text-xs text-slate-500 font-medium uppercase tracking-wide">{{ $s['label'] }}</div>
            </div>
          </div>
        @endforeach
      </div>

      {{-- FILTROS --}}
      <form method="GET" class="bg-white rounded-xl shadow p-4 flex flex-wrap gap-3 items-end">
        <div>
          <label class="block text-xs text-slate-500 mb-1">Buscar</label>
          <input name="search" value="{{ request('search') }}" placeholder="Título…"
                 class="border rounded-lg px-3 py-2 text-sm w-44">
        </div>
        <div>
          <label class="block text-xs text-slate-500 mb-1">Proveedor</label>
          <select name="proveedor_id" class="border rounded-lg px-3 py-2 text-sm" onchange="this.form.submit()">
            <option value="">Todos</option>
            @foreach($proveedores as $id => $nombre)
              <option value="{{ $id }}" @selected(request('proveedor_id') == $id)>{{ $nombre }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs text-slate-500 mb-1">Tipo</label>
          <select name="tipo" class="border rounded-lg px-3 py-2 text-sm" onchange="this.form.submit()">
            <option value="">Todos</option>
            @foreach(['Reclamo','Consulta','Seguimiento','Renovación'] as $t)
              <option value="{{ $t }}" @selected(request('tipo') === $t)>{{ $t }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs text-slate-500 mb-1">Estado</label>
          <select name="estado" class="border rounded-lg px-3 py-2 text-sm" onchange="this.form.submit()">
            <option value="">Todos</option>
            @foreach(['Pendiente','En curso','Cerrada'] as $e)
              <option value="{{ $e }}" @selected(request('estado') === $e)>{{ $e }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs text-slate-500 mb-1">Prioridad</label>
          <select name="prioridad" class="border rounded-lg px-3 py-2 text-sm" onchange="this.form.submit()">
            <option value="">Todas</option>
            @foreach(['Alta','Media','Baja'] as $p)
              <option value="{{ $p }}" @selected(request('prioridad') === $p)>{{ $p }}</option>
            @endforeach
          </select>
        </div>
        <button class="bg-slate-200 hover:bg-slate-300 px-4 py-2 rounded-lg text-sm font-medium transition">
          Filtrar
        </button>
        @if(request()->hasAny(['search','proveedor_id','tipo','estado','prioridad']))
          <a href="{{ route('tareas-proveedores.index') }}" class="text-sm text-slate-500 hover:underline self-end pb-2">
            Limpiar
          </a>
        @endif
      </form>

      {{-- TABLA --}}
      <div class="overflow-x-auto bg-white rounded-xl shadow">
        <table class="w-full text-sm border-collapse">
          <thead>
            <tr class="bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide border-b">
              <th class="px-4 py-3">Proveedor</th>
              <th class="px-4 py-3">Título</th>
              <th class="px-4 py-3">Tipo</th>
              <th class="px-4 py-3">Prioridad</th>
              <th class="px-4 py-3">Estado</th>
              <th class="px-4 py-3">Fecha carga</th>
              <th class="px-4 py-3">Vence</th>
              <th class="px-4 py-3 text-right">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($tareas as $t)
              @php
                $vencida = $t->estado !== 'Cerrada' && $t->fecha_limite && $t->fecha_limite->isPast();
              @endphp
              <tr class="border-b hover:bg-slate-50 transition {{ $vencida ? 'bg-red-50' : '' }}">
                <td class="px-4 py-3 font-medium text-slate-700">
                  {{ $t->proveedor->nombre ?? '—' }}
                </td>
                <td class="px-4 py-3 text-slate-800 max-w-xs">
                  <div class="truncate">{{ $t->titulo }}</div>
                </td>
                <td class="px-4 py-3">
                  <span class="inline-flex text-xs px-2 py-1 rounded-full font-medium {{ $t->colorTipo() }}">
                    {{ $t->tipo }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <span class="inline-flex text-xs px-2 py-1 rounded-full font-medium
                    {{ $t->prioridad === 'Alta' ? 'bg-red-100 text-red-700' : ($t->prioridad === 'Media' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                    {{ $t->prioridad }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <span class="inline-flex text-xs px-2 py-1 rounded-full font-medium {{ $t->colorEstado() }}">
                    {{ $t->estado }}
                  </span>
                </td>
                <td class="px-4 py-3 text-slate-500">
                  {{ optional($t->fecha_carga)->format('d/m/Y') }}
                </td>
                <td class="px-4 py-3">
                  @if($t->fecha_limite)
                    <span class="{{ $vencida ? 'text-red-600 font-semibold' : 'text-slate-500' }}">
                      {{ $t->fecha_limite->format('d/m/Y') }}
                      @if($vencida) ⚠️ @endif
                    </span>
                  @else
                    <span class="text-slate-300">—</span>
                  @endif
                </td>
                <td class="px-4 py-3 text-right">
                  <a href="{{ route('tareas-proveedores.show', $t) }}"
                     class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-lg border border-sky-300 text-sky-700 hover:bg-sky-50 transition">
                    Ver
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="px-4 py-12 text-center text-slate-400">
                  No hay tareas con los filtros aplicados.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div>{{ $tareas->links() }}</div>

    </div>
  </main>
</div>
</x-app-layout>

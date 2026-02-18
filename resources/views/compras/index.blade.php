{{-- resources/views/compras/index.blade.php --}}
<x-app-layout>
  <div class="flex">
    <x-sidebar />

    <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-8">
      <div class="max-w-6xl mx-auto space-y-10">

        {{-- ═════════════ CABECERA ═════════════ --}}
        <div class="flex items-center justify-between">
          <h1 class="text-2xl font-semibold text-emerald-700">
            @if(($modo ?? 'pendientes') === 'realizadas')
              Historial de compras realizadas
            @else
              Compras pendientes
            @endif
          </h1>

          <div class="flex gap-3">
            {{-- NUEVA --}}
            <a href="{{ route('compras.create') }}"
               class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded">
              + Nueva compra
            </a>

            @if(($modo ?? 'pendientes') === 'realizadas')
              {{-- ←  Volver a pendientes --}}
              <a href="{{ route('compras.index') }}"
                 class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded">
                Volver a pendientes
              </a>
            @else
              {{-- →  Ir al historial --}}
              <a href="{{ route('compras.realizadas') }}"
                 class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded">
                Historial compras
              </a>
            @endif

            {{-- HISTORIAL LEGACY --}}
            <a href="{{ route('compras.legacy.index') }}"
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded inline-flex items-center gap-2"
               title="Ver compras antiguas">
              <i class="fa-solid fa-clock-rotate-left"></i>
              Compras antiguas
            </a>
          </div>
        </div>

        {{-- FLASH --}}
        @if(session('success'))
          <div class="bg-emerald-100 border border-emerald-300 text-emerald-800 px-4 py-2 rounded">
            {{ session('success') }}
          </div>
        @endif

        {{-- ═════════════ FILTROS (comparten vista para ambos modos) ═════════════ --}}
        <form method="GET" class="flex flex-wrap gap-4 items-end bg-white p-4 rounded shadow">
          <div>
            <label class="block text-sm">Buscar</label>
            <input name="search" value="{{ request('search') }}"
                   class="border rounded px-3 py-2" placeholder="Producto…">
          </div>

          <div>
            <label class="block text-sm">Prioridad</label>
            <select name="prioridad" class="border rounded pl-5 py-2"
                    onchange="this.form.submit()">
              <option value="">Todas</option>
              @foreach($prioridades as $p)
                <option value="{{ $p }}" @selected(request('prioridad') == $p)>
                  {{ $p }}
                </option>
              @endforeach
            </select>
          </div>

          <button class="ml-auto bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded">
            Filtrar
          </button>
        </form>

        {{-- ═════════════ MODO “PENDIENTES” (a-realizar + en-proceso) ═════════════ --}}
        @if(($modo ?? 'pendientes') === 'pendientes')

          {{-- —— tabla parcial reutilizable —— --}}
          @php
            $renderTabla = function($items, $emptyMsg) {
              return view('compras._tabla',[ 'items' => $items , 'vacío' => $emptyMsg ])->render();
            };
          @endphp

          {{-- A REALIZAR --}}
          <section>
            <h2 class="font-semibold text-lg mb-2 text-amber-700">
              <i class="fa-solid fa-list-check"></i> Compra a realizar
            </h2>

            {!! $renderTabla($aRealizar, 'No hay compras a realizar.') !!}
            <div class="mt-2">{{ $aRealizar->links() }}</div>
          </section>

          {{-- EN PROCESO --}}
          <section>
            <h2 class="font-semibold text-lg mb-2 text-sky-700">
              <i class="fa-solid fa-arrows-spin"></i> Compra en proceso
            </h2>

            {!! $renderTabla($enProceso, 'No hay compras en proceso.') !!}
            <div class="mt-2">{{ $enProceso->links() }}</div>
          </section>

        @else {{-- ═════════════ MODO “REALIZADAS” ═════════════ --}}

          <section>
            {!! view('compras._tabla', [
                  'items' => $compras,
                  'vacío' => 'No hay compras realizadas.'
                ])->render() !!}
            <div class="mt-2">{{ $compras->links() }}</div>
          </section>

        @endif

      </div>
    </main>
  </div>
</x-app-layout>

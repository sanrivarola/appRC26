<x-app-layout>
  <div class="flex" style="color-scheme: light;">
    <x-sidebar />

    <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-8">
      <div class="max-w-6xl mx-auto space-y-8">

        {{-- CABECERA --}}
        <div class="flex items-center justify-between">
          <h1 class="text-2xl font-semibold text-emerald-700 flex items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left"></i>
            Compra antigua #{{ $compra->id_compras }}
          </h1>

          <div class="flex gap-3">
            <a href="{{ route('compras.legacy.index') }}"
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded">
              Volver a antiguas
            </a>
            <a href="{{ route('compras.index') }}"
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded">
              Compras actuales
            </a>
          </div>
        </div>

        {{-- TARJETA DE DATOS --}}
        <div class="bg-white shadow rounded-xl p-8 space-y-8">

          {{-- GRID INFO --}}
          <div class="grid md:grid-cols-2 gap-6">
            {{-- Fecha de carga --}}
            <div>
              <div class="text-sm text-slate-500">Fecha de carga</div>
              <div class="mt-1 font-medium">
                {{ optional($compra->fechacarga)->format('d/m/Y') }}
              </div>
            </div>

            {{-- Fecha de compra --}}
            <div>
              <div class="text-sm text-slate-500">Fecha de compra</div>
              <div class="mt-1 font-medium">
                {{ $compra->fechacompra ? $compra->fechacompra->format('d/m/Y') : '—' }}
              </div>
            </div>

            {{-- Área --}}
            <div>
              <div class="text-sm text-slate-500">Área</div>
              <div class="mt-1 font-medium">
                {{ $compra->area->nombre ?? '—' }}
              </div>
            </div>

            {{-- Prioridad (chip) --}}
            <div>
              <div class="text-sm text-slate-500">Prioridad</div>
              <div class="mt-1">
                <span @class([
                  'inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full',
                  'bg-red-200 text-red-700'      => $compra->prioridad==='Alta',
                  'bg-yellow-200 text-yellow-700'=> $compra->prioridad==='Media',
                  'bg-green-200 text-green-700'  => $compra->prioridad==='Baja',
                ])>
                  <i class="fa-solid fa-flag"></i> {{ $compra->prioridad }}
                </span>
              </div>
            </div>

            {{-- Estado (chip) --}}
            <div>
              <div class="text-sm text-slate-500">Estado</div>
              <div class="mt-1">
                <span @class([
                  'inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full',
                  'bg-green-200 text-green-700' => $compra->estado==='Compra realizada',
                  'bg-amber-200 text-amber-700' => $compra->estado==='Compra en proceso',
                  'bg-red-200 text-red-700'     => $compra->estado==='Compra a realizar',
                ])>
                  <i class="fa-solid fa-circle"></i> {{ $compra->estado }}
                </span>
              </div>
            </div>

            {{-- Responsable --}}
            <div>
              <div class="text-sm text-slate-500">Responsable</div>
              <div class="mt-1 font-medium">{{ $compra->responsable }}</div>
            </div>

            {{-- Creador --}}
            <div>
              <div class="text-sm text-slate-500">Creador</div>
              <div class="mt-1 font-medium">{{ $compra->creador }}</div>
            </div>
          </div>

          {{-- Producto (descripción) --}}
          <div>
            <div class="text-sm text-slate-500">Producto</div>
            <div class="mt-2 p-4 bg-slate-50 rounded border border-slate-200 leading-relaxed">
              {{ $compra->producto }}
            </div>
          </div>

        </div>
      </div>
    </main>
  </div>
</x-app-layout>

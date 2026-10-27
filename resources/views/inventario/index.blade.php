{{-- resources/views/inventario/index.blade.php --}}
<x-app-layout>
    <div class="flex">
        <x-sidebar />

        <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-4 md:p-8">
            <div class="max-w-7xl mx-auto space-y-6">

                {{-- ───── Encabezado + botón Áreas/Subáreas ───── --}}
                <section class="bg-white shadow rounded-2xl p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-semibold text-amber-700 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                            </svg>
                            Inventario
                        </h2>

                        @role('admin')
                            <a href="{{ route('areas.index') }}"
                               class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold transition">
                                <i class="fa-solid fa-layer-group"></i> Áreas | Subáreas
                            </a>
                        @endrole
                    </div>
                </section>

                {{-- ───── Alerta de bajo stock ───── --}}
                @if($lowStock->count())
                    <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-2 rounded shadow">
                        ⚠️ Hay {{ $lowStock->count() }} productos por debajo del stock mínimo.
                        <a href="{{ route('inventario.index', ['low_stock'=>1]) }}"
                           class="underline font-semibold">Ver listado de bajo stock</a>
                    </div>
                @endif

                {{-- ───── Filtros y controles ───── --}}
                <form method="GET"
                      class="flex flex-wrap gap-3 items-end bg-white shadow rounded-lg p-4">
                    {{-- Buscar --}}
                    <input name="search" value="{{ request('search') }}"
                           placeholder="Buscar producto…"
                           class="border rounded px-3 py-1 flex-1" />

                    {{-- Área --}}
                    <select name="area_id"
                            class="border rounded px-3 py-1"
                            onchange="
                                this.form.subarea_id.value='';
                                this.form.submit();
                            ">
                        <option value="">Todas las áreas</option>
                        @foreach($areas as $a)
                            <option value="{{ $a->id_areainventario }}"
                                    @selected(request('area_id') == $a->id_areainventario)>
                                {{ $a->nombre }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Subárea --}}
                    <select name="subarea_id" class="border rounded px-3 py-1"
                            onchange="this.form.submit()">
                        <option value="">Todas las subáreas</option>
                        @foreach($subareas as $s)
                            <option value="{{ $s->id_subareasinventario }}"
                                    @selected(request('subarea_id') == $s->id_subareasinventario)>
                                {{ $s->nombre }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Ítems por página --}}
                    <select name="per_page" class="border rounded px-3 py-1"
                            onchange="this.form.submit()">
                        @foreach([25,50,100] as $size)
                            <option value="{{ $size }}" @selected(request('per_page',25)==$size)>
                                {{ $size }}
                            </option>
                        @endforeach
                    </select>

                    @role('admin')
                        {{-- Todo --}}
                        <a href="{{ route('inventario.index') }}"
                           class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded">
                            Todo
                        </a>

                        {{-- + Nuevo --}}
                        <a href="{{ route('inventario.create') }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                            + Nuevo
                        </a>
                        
                        <!--<a href="{{ route('inventario.export.xlsx', request()->query()) }}"-->
                        <!--   class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded shadow">-->
                        <!--    Exportar Excel-->
                        <!--</a>-->
                    @endrole
                </form>

                {{-- ───── Tabla ───── --}}
                <div class="overflow-x-auto bg-white shadow rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2">Producto</th>
                                <th class="px-4 py-2">Área</th>
                                <th class="px-4 py-2">Subárea</th>
                                <th class="px-4 py-2 text-center">Cant.</th>
                                <th class="px-4 py-2 text-center">Mín.</th>
                                <th class="px-4 py-2 text-center">Historial</th>
                                <th class="px-4 py-2 text-center">± Stock</th>
                                <th class="px-4 py-2 text-center">Comprar</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @foreach($inventarios as $item)
                                <tr @class(['bg-red-50'=>$item->cantidad < $item->stockminimo])>
                                    <td class="px-4 py-2">{{ $item->nombreproducto }}</td>
                                    <td class="px-4 py-2">{{ $item->area->nombre ?? '—' }}</td>
                                    <td class="px-4 py-2">{{ $item->subarea->nombre ?? '—' }}</td>
                                    <td class="px-4 py-2 text-center">{{ $item->cantidad }}</td>
                                    <td class="px-4 py-2 text-center">{{ $item->stockminimo }}</td>

                                    {{-- Historial --}}
                                    <td class="px-4 py-2 text-center">
                                        <a href="{{ route('inventario.history', $item) }}"
                                           class="text-indigo-600 hover:underline"
                                           title="Ver historial">
                                            <i class="fa-solid fa-list"></i>
                                        </a>
                                    </td>

                                    {{-- ± Stock --}}
                                    <td class="px-4 py-2 text-center space-x-1">
                                        <form action="{{ route('inventario.adjust',$item) }}"
                                              method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="delta" value="1">
                                            <button class="bg-green-500 hover:bg-green-600 text-white px-2 rounded"
                                                    title="Sumar">＋</button>
                                        </form>

                                        <form action="{{ route('inventario.adjust',$item) }}"
                                              method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="delta" value="-1">
                                            <button class="bg-red-500 hover:bg-red-600 text-white px-2 rounded"
                                                    title="Restar">－</button>
                                        </form>
                                    </td>

                                    {{-- Comprar --}}
                                    <td class="px-4 py-2 text-center">
                                        <a href="{{ route('compras.create', [
                                                'producto' => $item->nombreproducto,
                                                'area_id'  => $item->area_id
                                            ]) }}"
                                           class="inline-flex items-center gap-1
                                                  bg-amber-500 hover:bg-amber-600 text-white
                                                  px-3 py-1 rounded text-xs"
                                           title="Crear pedido de compra">
                                            <i class="fa-solid fa-cart-plus"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ───── Paginación ───── --}}
                <div>
                    {{ $inventarios->appends(request()->query())->links() }}
                </div>

            </div>
        </main>
    </div>
</x-app-layout>

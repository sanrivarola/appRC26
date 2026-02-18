{{-- resources/views/proveedores/index.blade.php --}}
<x-app-layout>
    <div class="flex">
        <x-sidebar />

        <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-4 md:p-8">
            <div class="max-w-6xl mx-auto space-y-6">

                {{-- CABECERA ------------------------------------------------------------------}}
                <div class="bg-white shadow rounded-2xl p-6 space-y-4 md:space-y-0
                            flex flex-col md:flex-row md:items-center md:justify-between">

                    <h2 class="text-2xl font-semibold text-violet-700 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>
                        Proveedores
                    </h2>

                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('proveedores.create') }}"
                           class="bg-violet-700 hover:bg-violet-600 text-white px-4 py-2 rounded flex items-center gap-2">
                            <i class="fa-solid fa-plus"></i> Nuevo
                        </a>

                        <a href="{{ route('proveedores.export') }}"
                        target="_blank"
                        class="bg-emerald-600 hover:bg-emerald-500 text-white px-3 py-2 rounded flex items-center gap-2">
                            <i class="fa-solid fa-file-arrow-down"></i> Exportar
                        </a>
                    </div>
                </div>

                {{-- FORM FILTROS -------------------------------------------------------------}}
                <form method="GET" action="{{ route('proveedores.index') }}"
                      class="bg-white shadow rounded-2xl p-6 flex flex-wrap gap-4">

                    <input  type="text"  name="buscar" value="{{ $search }}"
                            placeholder="Buscar proveedor…"
                            class="border rounded px-3 py-2 w-56">

                    <select name="area" class="border rounded px-53 py-2">
                        <option value="">Todas las áreas</option>

                        @foreach($areas as $a)
                            @continue(empty($a))  {{-- ignora valores vacíos si quedara alguno --}}
                            <option value="{{ $a }}" @selected($a == $area)>{{ $a }}</option>
                        @endforeach
                    </select>

                    <select name="perPage" class="border rounded px-6 py-2">
                        @foreach([10,25,100] as $n)
                            <option value="{{ $n }}" @selected($perPage==$n)>{{ $n }}</option>
                        @endforeach
                    </select>

                    <button type="submit"
                            class="bg-violet-700 hover:bg-violet-600 text-white px-4 py-2 rounded font-semibold">
                        Filtrar
                    </button>
                </form>

                {{-- FLASH --------------------------------------------------------------------}}
                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-300 text-emerald-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- TABLA --------------------------------------------------------------------}}
                <div class="bg-white shadow rounded-2xl p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-violet-100 text-violet-700">
                            <tr>
                                <th class="p-2">Fecha</th>
                                <th class="p-2">Nombre</th>
                                <th class="p-2">Área</th>
                                <th class="p-2">Responsable</th>
                                <th class="p-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($proveedores as $prov)
                            <tr class="hover:bg-violet-50">
                                <td class="p-2 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($prov->fechacarga)->format('d/m/Y') }}
                                </td>

                                {{-- enlace al SHOW --}}
                                <td class="p-2">
                                    <a href="{{ route('proveedores.show', $prov) }}"
                                       class="font-semibold text-violet-700 hover:underline">
                                        {{ $prov->nombre }}
                                    </a>
                                </td>

                                <td class="p-2">{{ $prov->area }}</td>
                                <td class="p-2">{{ $prov->responsable }}</td>

                                <td class="p-2 whitespace-nowrap space-x-2">
                                    <a href="{{ route('proveedores.edit', $prov) }}"
                                       class="text-amber-500 hover:text-amber-700" title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <form action="{{ route('proveedores.destroy', $prov) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('¿Borrar proveedor &quot;{{ $prov->nombre }}&quot;?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Eliminar"
                                                class="text-red-600 hover:text-red-800">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-4 text-center text-gray-500">
                                Sin resultados…
                            </td></tr>
                        @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $proveedores->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>

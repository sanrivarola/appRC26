{{-- resources/views/relevamientos/index.blade.php --}}
<x-app-layout>
    <div class="flex">
        <x-sidebar />

        <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-4 md:p-8 transition-all">
            <div class="max-w-6xl mx-auto space-y-6">

                {{-- ─────────────────────  ENCABEZADO  ───────────────────── --}}
                <div class="bg-white shadow rounded-2xl p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                    {{-- Título --}}
                    <h2 class="text-2xl font-semibold text-emerald-700 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 7h2l2-3h10l2 3h2a2 2 0 012 2v9a2 2 0 01-2 2H3a2 2 0 01-2-2V9a2 2 0 012-2z"/>
                            <circle cx="12" cy="13" r="4"/>
                        </svg>
                        Relevamientos
                    </h2>

                    {{-- Botones + filtros --}}
                    <div class="flex flex-wrap gap-3 items-end">

                        {{-- Nuevo --}}
                        <a href="{{ route('relevamientos.create') }}"
                           class="inline-flex items-center gap-2 bg-emerald-700 hover:bg-emerald-600 text-white px-4 py-2 rounded font-semibold transition">
                            <i class="fa-solid fa-plus"></i> Nuevo relevamiento
                        </a>

                        {{-- Selector cantidad / mantiene filtros existentes --}}
                        <form>
                            @foreach(request()->except('perPage','page') as $key => $val)
                                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                            @endforeach

                            <label class="text-sm text-gray-600 mr-1" for="perPage">Mostrar:</label>
                            <select name="perPage" id="perPage"
                                    class="border rounded px-8 py-1"
                                    onchange="this.form.submit()">
                                <option value="25" @selected($perPage==25)>25</option>
                                <option value="50" @selected($perPage==50)>50</option>
                                <option value="100"@selected($perPage==100)>100</option>
                            </select>
                        </form>
                    </div>
                </div>

                {{-- ─────────────────────  FLASH  ───────────────────── --}}
                @if(session('success'))
                    <div class="bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded relative">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- ─────────────────────  TABLA  ───────────────────── --}}
                <div class="bg-white shadow rounded-2xl p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-emerald-100 text-emerald-700">
                            <tr>
                                <th class="p-2 font-bold">Fecha</th>
                                <th class="p-2 font-bold">Título</th>

                                {{-- Columna EQUIPO: visible siempre para Admin; en los demás roles oculta en móvil --}}
                                @php
                                    $eqThClass = auth()->user()->hasRole('admin')
                                        ? 'p-2 font-bold'
                                        : 'p-2 font-bold hidden md:table-cell';
                                @endphp
                                <th class="{{ $eqThClass }}">Equipo</th>

                                <th class="p-2 font-bold">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($relevamientos as $rel)
                            <tr class="hover:bg-emerald-50">
                                <td class="p-2 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($rel->fechacarga)->format('d/m/Y') }}
                                </td>

                                <td class="p-2">
                                    <a href="{{ route('relevamientos.show', $rel->id_relevamiento) }}"
                                       class="font-semibold text-emerald-700 hover:underline">
                                        {{ $rel->titulorelevamiento }}
                                    </a>
                                </td>

                                {{-- Celda equipo con la misma lógica responsive --}}
                                @php
                                    $eqTdClass = auth()->user()->hasRole('admin')
                                        ? 'p-2'
                                        : 'p-2 hidden md:table-cell';
                                @endphp
                                <td class="{{ $eqTdClass }}">{{ $rel->equipo }}</td>

                                <td class="p-2 space-x-2 whitespace-nowrap">



                                    @role('admin')
                                        <a href="{{ route('relevamientos.export', $rel->id_relevamiento) }}"
                                        class="text-emerald-600 hover:text-emerald-800"
                                        title="PDF">
                                            <i class="fa-solid fa-file-arrow-down"></i>
                                        </a>
                                    @endrole


                                    {{-- Editar --}}
                                    <a href="{{ route('relevamientos.edit', $rel->id_relevamiento) }}"
                                       class="text-amber-500 hover:text-amber-700"
                                       title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    {{-- Eliminar --}}
                                    <form  action="{{ route('relevamientos.destroy', $rel->id_relevamiento) }}"
                                        method="POST"  class="inline"
                                        onsubmit="return confirm('¿Borrar el relevamiento &quot;{{ addslashes($rel->titulorelevamiento) }}&quot;?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-800"
                                                title="Eliminar">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-4 text-center text-gray-500">
                                    No se encontraron relevamientos.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    <div class="mt-6">
                        {{ $relevamientos->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>

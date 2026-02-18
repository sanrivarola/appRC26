{{-- resources/views/areas/index.blade.php --}}
<x-app-layout>
    <div class="flex">
        <x-sidebar />

        <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-4 md:p-8">
            <div class="max-w-6xl mx-auto space-y-6">

                {{-- CABECERA --}}
                <div class="bg-white shadow rounded-xl p-4 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-yellow-700 flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
</svg>

                        Áreas
                    </h2>

                    <a href="{{ route('areas.create') }}"
                       class="inline-flex items-center gap-1 bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1.5 rounded-lg shadow">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none"
                             viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Nueva
                    </a>
                     <a href="{{ route('inventario.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                        <i class="fa-solid fa-arrow-left"></i> Volver
                    </a>
                </div>

                {{-- FLASH --}}
                @if(session('success'))
                    <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-2 rounded shadow">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- LISTADO ÁREAS --}}
                <div x-data="{ open: {{ request('open') ? (int)request('open') : 'null' }} }"
    class="overflow-x-auto bg-white shadow rounded-xl">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold">Área</th>
                                <th class="px-4 py-2 text-center font-semibold">Subáreas</th>
                                <th class="px-4 py-2 text-center font-semibold">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($areas as $area)
                                {{-- FILA ÁREA --}}
                                <tr class="hover:bg-violet-50"
                                    :class="{ 'bg-violet-50': open === {{ $area->id_areainventario }} }">
                                    <td class="px-4 py-2">
                                        <button @click="open = open === {{ $area->id_areainventario }} ? null : {{ $area->id_areainventario }}"
                                                class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-gray-500 transform"
                                                 :class="open === {{ $area->id_areainventario }} ? 'rotate-180' : ''"
                                                 fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M6 9l6 6 6-6" />
                                            </svg>
                                            {{ $area->nombre }}
                                        </button>
                                    </td>

                                    <td class="px-4 py-2 text-center">
                                        <span class="inline-block min-w-[2rem] px-2 bg-violet-100 text-violet-700 rounded-full">
                                            {{ $area->subareas->count() }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-2 flex items-center justify-center gap-3">
                                        <a href="{{ route('areas.edit', $area->id_areainventario) }}"
                                           class="text-yellow-600 hover:text-yellow-800" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none"
                                                 viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M6 18H18M7.5 11.5L16.5 2.5m-2 14L4 22l2-6z" />
                                            </svg>
                                        </a>

                                        <form action="{{ route('areas.destroy', $area->id_areainventario) }}"
                                              method="POST"
                                              onsubmit="return confirm('¿Eliminar área y subáreas?');">
                                            @csrf @method('DELETE')
                                            <button class="text-red-600 hover:text-red-800" title="Eliminar">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none"
                                                     viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- SUBÁREAS DROPDOWN --}}
                                <tr x-show="open === {{ $area->id_areainventario }}" x-collapse>
                                    <td colspan="3" class="bg-gray-50 px-6 py-2">
                                        <ul class="space-y-1">
                                            @foreach($area->subareas as $subarea)
                                                <li class="flex items-center justify-between py-1 px-2 hover:bg-gray-100 rounded">
                                                    <span>{{ $subarea->nombre }}</span>
                                                    <span class="flex items-center gap-2">
                                                        <a href="{{ route('subareas.edit', ['subarea' => $subarea->id_subareasinventario]) }}"
                                                           class="text-yellow-600 hover:text-yellow-800" title="Editar">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none"
                                                                 viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      d="M6 18H18M7.5 11.5L16.5 2.5m-2 14L4 22l2-6z" />
                                                            </svg>
                                                        </a>
                                                        <form action="{{ route('subareas.destroy', ['subarea' => $subarea->id_subareasinventario]) }}"
                                                              method="POST"
                                                              onsubmit="return confirm('¿Eliminar subárea?');">
                                                            @csrf @method('DELETE')
                                                            <button class="text-red-600 hover:text-red-800" title="Eliminar">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none"
                                                                     viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                          d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>

                                        <div class="mt-2">
                                            <a href="{{ route('subareas.create', ['area_id' => $area->id_areainventario]) }}"
                                               class="inline-flex items-center gap-1 text-violet-600 hover:text-violet-800 text-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none"
                                                     viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M12 4.5v15m7.5-7.5h-15" />
                                                </svg>
                                                Añadir subárea
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>

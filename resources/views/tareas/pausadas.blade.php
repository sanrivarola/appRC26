{{-- resources/views/tareas/pausadas.blade.php --}}

<x-app-layout>
    <div class="flex">
        <x-sidebar />

        <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-4 md:p-8 transition-all">
            <div class="max-w-6xl mx-auto mt-6">
                <div class="bg-white shadow rounded-2xl p-6">
                    {{-- Navegación para admin/creador --}}
                    @role('admin|creador')
                        <div class="mb-4 flex gap-2">
                            <a href="{{ route('tareas.index') }}"
                               class="bg-green-500 text-white px-3 py-2 rounded hover:bg-green-700 font-semibold">Tareas Activas</a>
                            <a href="{{ route('tareas.pausadas') }}"
                               class="bg-purple-500 text-white px-3 py-2 rounded hover:bg-purple-700 font-semibold">Tareas Pausadas</a>
                            <a href="{{ route('tareas.archivadas') }}"
                               class="bg-yellow-400 text-black px-3 py-2 rounded hover:bg-yellow-500 font-semibold">Tareas Archivadas</a>
                        </div>
                    @endrole

                    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
                        <h2 class="text-2xl font-semibold text-purple-700 flex items-center gap-2">
                            <i class="fa-solid fa-pause-circle"></i>
                            Tareas Pausadas
                        </h2>
                        <form action="{{ route('tareas.pausadas') }}" method="GET" class="flex items-center gap-2">
                            <label for="seleccionardia" class="text-gray-700 mr-2">Sólo mostrar tareas de:</label>
                            <select name="seleccionardia" id="seleccionardia" class="rounded border-gray-300 px-4 py-3 w-52">
                                <option value="">Toda la semana</option>
                                <option value="lunes" @selected($diaFiltro=='lunes')>Lunes</option>
                                <option value="martes" @selected($diaFiltro=='martes')>Martes</option>
                                <option value="miercoles" @selected($diaFiltro=='miercoles')>Miércoles</option>
                                <option value="jueves" @selected($diaFiltro=='jueves')>Jueves</option>
                                <option value="viernes" @selected($diaFiltro=='viernes')>Viernes</option>
                                <option value="sabado" @selected($diaFiltro=='sabado')>Sábado</option>
                                <option value="domingo" @selected($diaFiltro=='domingo')>Domingo</option>
                            </select>
                            @role('admin')
                                <input
                                    type="text"
                                    name="buscar"
                                    placeholder="Buscar tarea o responsable..."
                                    value="{{ request('buscar') }}"
                                    class="rounded border-gray-300 px-3 py-2"
                                >
                            @endrole
                            <button type="submit" class="bg-purple-700 text-white rounded px-4 py-2 font-semibold hover:bg-purple-800 transition">Filtrar</button>
                        </form>
                    </div>

                    <span class="block text-gray-500 mb-4 text-sm">{{ $mensajeday }}</span>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-purple-100 text-purple-800">
                                <tr>
                                    <th class="p-2 font-bold">Fecha</th>
                                    <th class="p-2 font-bold">Tarea pausada</th>
                                    <th class="p-2 font-bold">Prioridad</th>
                                    <th class="p-2 font-bold">Días</th>
                                    @if(auth()->user()->hasRole('admin'))
                                        <th class="p-2 font-bold">Responsable</th>
                                        <th class="p-2 font-bold">Devoluciones</th>
                                        <th class="p-2 font-bold">Estado</th>
                                        <th class="p-2 font-bold">Editar</th>
                                        <th class="p-2 font-bold">Borrar</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tareas as $tarea)
                                    <tr class="hover:bg-indigo-50">
                                        <td class="p-2">{{ $tarea->fecha_carga }}</td>
                                        <td class="p-2">
                                            <a href="{{ route('tareas.show', $tarea->id_tarea) }}" class="text-purple-700 font-bold hover:underline">
                                                {{ $tarea->tarea }}
                                            </a>
                                        </td>
                                        <td class="p-2">
                                            @if($tarea->prioridad === 'Alta')
                                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded font-bold text-xs">Alta</span>
                                            @elseif($tarea->prioridad === 'Media')
                                                <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded font-bold text-xs">Media</span>
                                            @else
                                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded font-bold text-xs">Baja</span>
                                            @endif
                                        </td>
                                        <td class="p-2">
                                            @foreach(['lunes','martes','miercoles','jueves','viernes','sabado','domingo'] as $dia)
                                                @if($tarea->$dia === 'si')
                                                    <span class="inline-block text-xs bg-indigo-200 text-indigo-800 rounded px-2 py-1 mr-1">{{ strtoupper(substr($dia,0,3)) }}</span>
                                                @endif
                                            @endforeach
                                            @if($tarea->norepite === 'si')
                                                <span class="inline-block text-xs bg-pink-200 text-pink-800 rounded px-2 py-1 ml-1">(ÚNICA VEZ)</span>
                                            @endif
                                        </td>
                                        @if(auth()->user()->hasAnyRole(['admin', 'editor']))
                                            <td class="p-2">{{ $tarea->equipo }}</td>
                                            <td class="p-2">
                                                <a href="{{ route('tareas.devoluciones', $tarea->id_tarea) }}" class="text-indigo-600 hover:underline">
                                                    <i class="fa-solid fa-list-check"></i>
                                                </a>
                                            </td>
                                            <td class="p-2">
                                                @if($tarea->estado==2)
                                                    <i class="fa-solid fa-toggle-off text-purple-700"></i>
                                                @else
                                                    <i class="fa-solid fa-toggle-on text-gray-400"></i>
                                                @endif
                                            </td>
                                            <td class="p-2">
                                                <a href="{{ route('tareas.edit', $tarea->id_tarea) }}"><i class="fa-solid fa-pen-to-square text-indigo-600"></i></a>
                                            </td>
                                            <td class="p-2">
                                                <form action="{{ route('tareas.destroy', $tarea->id_tarea) }}" method="POST" onsubmit="return confirm('¿Borrar tarea?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"><i class="fa-solid fa-trash-can text-red-600"></i></button>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="p-4 text-center text-gray-500">No se encontraron tareas pausadas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $tareas->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>

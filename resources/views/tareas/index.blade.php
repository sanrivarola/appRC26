<x-app-layout>
    <div class="flex">
        <x-sidebar />

        <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-4 md:p-8 transition-all">
            <div class="max-w-6xl mx-auto mt-6">

                {{-- ✨ EXPORT & FILTROS ✨ --}}
                        @role('admin')
                        <form action="{{ route('tareas.export') }}" method="POST" class="flex flex-wrap gap-4 items-end mb-6">
                            @csrf
                        
                            <div>
                                <label for="fecha_desde" class="block text-sm font-semibold mb-1">Desde</label>
                                <input
                                    type="date"
                                    id="fecha_desde"
                                    name="fecha_desde"
                                    value="{{ request('fecha_desde') }}"
                                    class="rounded border-gray-300 px-3 py-2"
                                    required
                                >
                            </div>
                        
                            <div>
                                <label for="fecha_hasta" class="block text-sm font-semibold mb-1">Hasta</label>
                                <input
                                    type="date"
                                    id="fecha_hasta"
                                    name="fecha_hasta"
                                    value="{{ request('fecha_hasta') }}"
                                    class="rounded border-gray-300 px-3 py-2"
                                    required
                                >
                            </div>
                        
                            <div>
                                <label for="equipo" class="block text-sm font-semibold mb-1">Responsable</label>
                                <select
                                    id="equipo"
                                    name="equipo"
                                    class="rounded border-gray-300 px-3 py-2"
                                    required
                                >
                                    @php $selectedEquipo = request('equipo', 'todos'); @endphp
                                    <option value="todos" {{ $selectedEquipo === 'todos' ? 'selected' : '' }}>Todos</option>
                                    @foreach($equipos as $equipo)
                                        <option value="{{ $equipo }}" {{ $selectedEquipo === $equipo ? 'selected' : '' }}>
                                            {{ $equipo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        
                            <div class="flex items-center h-[42px] pl-1">
                                <input
                                    type="checkbox"
                                    id="solo_finalizadas"
                                    name="solo_finalizadas"
                                    value="1"
                                    class="rounded border-gray-300 mr-2"
                                    {{ request('solo_finalizadas') ? 'checked' : '' }}
                                >
                                <label for="solo_finalizadas" class="text-sm font-semibold select-none">
                                    Solo finalizadas
                                </label>
                            </div>
                        
                            <button
                                type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold"
                            >
                                Exportar Excel
                            </button>
                        </form>
                        @endrole




                {{-- ✨ EXTRA TAREAS (colaborador o admin especiales) ✨ --}}
                @php
                    $user = auth()->user();
                    $isSpecialAdmin = $user->hasRole('admin') && in_array($user->name, ['Jessica','Elisa']);
                    $isColaborador  = $user->hasRole('colaborador');
                @endphp

                @if($isColaborador || $isSpecialAdmin)
                    {{-- 1️⃣ TAREAS REALIZADAS HOY --}}
                    <div class="bg-white shadow rounded-2xl p-6 mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-semibold text-teal-700">Tareas realizadas hoy</h3>
                            {{-- Antes tenías: route('tareas.extras.create', $tarea->id_tarea) --}}
                            <a href="{{ route('tareas.extras.create', 94) }}"
                            class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded">
                                + Agregar Extra Tareas
                            </a>
                        </div>
                        @if($extrasRealizadas->isEmpty())
                            <p class="text-gray-500">No se registraron tareas hoy.</p>
                        @else
                            <ul class="space-y-4">
                                @foreach($extrasRealizadas as $extra)
                                    <li class="border-b pb-2">
                                        <div class="flex justify-between">
                                            <span class="font-medium">{{ $extra->tarea }}</span>
                                            <span class="text-gray-500 text-sm">{{ $extra->fecha }}</span>
                                        </div>
                                        <p class="mt-1 text-gray-700">{{ $extra->devolucion }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    {{-- 2️⃣ TAREAS NO REALIZADAS HOY --}}
                    <div class="bg-white shadow rounded-2xl p-6 mb-6">
                        <h3 class="text-xl font-semibold text-orange-700 mb-4">Tareas no realizadas hoy</h3>
                        @if($extrasNoRealizadas->isEmpty())
                            <p class="text-gray-500">Todas las tareas planificadas para hoy están registradas.</p>
                        @else
                            <ul class="space-y-4">
                                @foreach($extrasNoRealizadas as $extra)
                                    <li class="border-b pb-2">
                                        <div class="flex justify-between">
                                            <span class="font-medium">{{ $extra->tarea }}</span>
                                            <span class="text-gray-500 text-sm">{{ $extra->fecha }}</span>
                                        </div>
                                        <p class="mt-1 text-gray-700">{{ $extra->devolucion }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif

                {{-- 3️⃣ PANEL PRINCIPAL DE TAREAS --}}
                <div class="bg-white shadow rounded-2xl p-6">

                    {{-- Botones solo admin/creador --}}
                    @role('admin|creador')
                        <div class="mb-4 flex flex-wrap gap-2">
                            <a href="{{ route('tareas.create') }}"
                               class="bg-green-700 hover:bg-green-500 text-white px-3 py-2 rounded font-semibold">
                                + Nueva tarea
                            </a>
                            <a href="{{ route('tareas.index') }}"
                               class="bg-green-500 hover:bg-green-700 text-white px-3 py-2 rounded font-semibold">
                                Tareas Activas
                            </a>
                            <a href="{{ route('tareas.pausadas') }}"
                               class="bg-purple-500 hover:bg-purple-700 text-white px-3 py-2 rounded font-semibold">
                                Tareas Pausadas
                            </a>
                            <a href="{{ route('tareas.archivadas') }}"
                               class="bg-yellow-400 hover:bg-yellow-500 text-black px-3 py-2 rounded font-semibold">
                                Tareas Archivadas
                            </a>
                        </div>
                    @endrole

                    {{-- Filtros + buscador --}}
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
                        <h2 class="text-2xl font-semibold text-green-600">Panel de Tareas activas</h2>
                        <form action="{{ route('tareas.index') }}" method="GET"
                              class="flex flex-wrap items-center gap-2">
                            <label for="seleccionardia" class="text-gray-700">Sólo mostrar:</label>
                            <select name="seleccionardia" id="seleccionardia"
                                    class="rounded border-gray-300 w-40 pr-8 pl-2 py-2 text-left">
                            <option value="">Toda la semana</option>
                            @foreach(['lunes','martes','miercoles','jueves','viernes','sabado','domingo'] as $dia)
                                <option value="{{ $dia }}"
                                        @selected(request('seleccionardia') === $dia)>
                                    {{ ucfirst($dia) }}
                                </option>
                            @endforeach
                            </select>

                            <input type="text"
                                   name="buscar"
                                   placeholder="Buscar tarea o responsable…"
                                   value="{{ request('buscar') }}"
                                   class="rounded border-gray-300 px-3 py-2"
                            >

                            <button type="submit"
                                    class="bg-green-600 hover:bg-green-700 text-white rounded px-4 py-2 font-semibold">
                                Filtrar
                            </button>
                        </form>
                    </div>

                    {{-- Mensaje diario --}}
                    <span class="text-gray-500 mb-4 block text-sm">{{ $mensajeday }}</span>

                    {{-- Tabla de tareas --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-green-100 text-green-700">
                                <tr>
                                    <th class="p-2 font-bold">Fecha</th>
                                    <th class="p-2 font-bold">Tarea</th>
                                    <th class="p-2 font-bold">Prioridad</th>
                                    <th class="p-2 font-bold">Días</th>
                                    @role('admin')
                                        <th class="p-2 font-bold">Responsable</th>
                                        <th class="p-2 font-bold">Devoluciones</th>
                                        <th class="p-2 font-bold">Estado</th>
                                        <th class="p-2 font-bold">Editar</th>
                                        <th class="p-2 font-bold">Borrar</th>
                                    @endrole
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tareas as $tarea)
                                    <tr class="hover:bg-indigo-50">
                                        <td class="p-2">{{ \Carbon\Carbon::parse($tarea->fecha_carga)->format('d-m-Y') }}</td>
                                        <td class="p-2">
                                            <a href="{{ route('tareas.show', $tarea->id_tarea) }}"
                                               class="text-green-600 font-bold hover:underline">
                                                {{ $tarea->tarea }}
                                            </a>
                                        </td>
                                        <td class="p-2">
                                            @switch($tarea->prioridad)
                                                @case('Alta')
                                                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">Alta</span>
                                                    @break
                                                @case('Media')
                                                    <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs font-bold">Media</span>
                                                    @break
                                                @default
                                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">Baja</span>
                                            @endswitch
                                        </td>
                                        <td class="p-2">
                                            @foreach(['lunes','martes','miercoles','jueves','viernes','sabado','domingo'] as $dia)
                                                @if($tarea->$dia === 'si')
                                                    <span class="inline-block bg-indigo-200 text-indigo-800 text-xs rounded px-2 py-1 mr-1">
                                                        {{ strtoupper(substr($dia,0,3)) }}
                                                    </span>
                                                @endif
                                            @endforeach
                                            @if($tarea->norepite === 'si')
                                                <span class="inline-block bg-pink-200 text-pink-800 text-xs rounded px-2 py-1 ml-1">
                                                    (ÚNICA)
                                                </span>
                                            @endif
                                        </td>

                                        @role('admin|editor')
                                            <td class="p-2">{{ $tarea->equipo }}</td>
                                            <td class="p-2 text-center">
                                                <a href="{{ route('tareas.devoluciones', $tarea->id_tarea) }}"
                                                   class="text-indigo-600 hover:underline">
                                                    <i class="fa-solid fa-list-check"></i>
                                                </a>
                                            </td>
                                            <td class="p-2 text-center">
                                                @if($tarea->estado)
                                                    <i class="fa-solid fa-toggle-on text-green-600"></i>
                                                @else
                                                    <i class="fa-solid fa-toggle-off text-red-600"></i>
                                                @endif
                                            </td>
                                            <td class="p-2 text-center">
                                                <a href="{{ route('tareas.edit', $tarea->id_tarea) }}">
                                                    <i class="fa-solid fa-pen-to-square text-indigo-600"></i>
                                                </a>
                                            </td>
                                            <td class="p-2 text-center">
                                                <form action="{{ route('tareas.destroy', $tarea->id_tarea) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('¿Borrar la tarea &laquo;{{ addslashes($tarea->tarea) }}&raquo;?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                                </form>
                                            </td>
                                        @endrole
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="p-4 text-center text-gray-500">
                                            No se encontraron tareas.
                                        </td>
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

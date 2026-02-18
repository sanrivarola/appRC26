<x-app-layout>
    <div class="flex">
        <x-sidebar />

        <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-4 md:p-8">
            <div class="max-w-5xl mx-auto">
                <h1 class="text-2xl font-bold text-orange-600 mb-6 flex items-center gap-2">
                    <svg class="h-7 w-7 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                    Ver horarios cargados
                </h1>

                <!-- Filtros -->
                <form method="GET" action="{{ route('horarios.index') }}"
                      class="flex flex-wrap gap-4 items-end mb-4 bg-white shadow p-4 rounded-xl">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Desde</label>
                        <input type="date" name="fecha1" class="form-input rounded border-gray-300"
                               value="{{ request('fecha1') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Hasta</label>
                        <input type="date" name="fecha2" class="form-input rounded border-gray-300"
                               value="{{ request('fecha2') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Equipo</label>
                        <select name="equipo" class="form-select rounded border-gray-300">
                            <option value="todos">Todos</option>
                            @foreach($equipos as $equipo)
                                <option value="{{ $equipo }}" @selected(request('equipo') == $equipo)>
                                    {{ $equipo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded">
                        Aplicar filtros
                    </button>
                </form>

                <!-- Botón Exportar Excel -->
                <form method="POST" action="{{ route('horarios.export') }}">
                    @csrf
                    <input type="hidden" name="fecha1" value="{{ request('fecha1') }}">
                    <input type="hidden" name="fecha2" value="{{ request('fecha2') }}">
                    <input type="hidden" name="equipo" value="{{ request('equipo') }}">
                    <input type="hidden" name="user_id" value="{{ request('user_id') }}">

                    <button type="submit"
                            class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-6 rounded-xl flex items-center gap-2 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                        </svg>
                        Exportar horarios (Excel)
                    </button>
                </form>

                <!-- Tabla horarios -->
                <div class="mb-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 rounded shadow-sm">
                    <strong>Atención:</strong> Solo se están mostrando los registros de los últimos <strong>60 días</strong>.
                    Si querés ver un rango más amplio, usá la opción de <strong>Exportar horarios</strong>.
                </div>
                <div class="overflow-x-auto bg-white shadow rounded-xl p-4">
                    <table class="min-w-full text-sm">
                        <thead>
                        <tr class="bg-orange-100 text-orange-700 text-left">
                            <th class="p-2 font-bold">Equipo</th>
                            <th class="p-2 font-bold">Fecha</th>
                            <th class="p-2 font-bold">Horario Entrada</th>
                            <th class="p-2 font-bold">Horario Salida</th>
                            <th class="p-2 font-bold">Total Horas</th>
                            <th class="p-2 font-bold">Estado</th>
                            <th class="p-2 font-bold">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($horarios as $row)
                            <tr class="@if(!$row->horarioentrada || !$row->horariosalida) bg-yellow-50 @endif hover:bg-orange-50">
                                <td class="p-2">{{ $row->equipo }}</td>
                                <td class="p-2">{{ \Carbon\Carbon::parse($row->dia)->format('d/m/Y') }}</td>
                                <td class="p-2">
                                    {{ $row->horarioentrada ? \Carbon\Carbon::parse($row->horarioentrada)->format('H:i:s') : '-' }}
                                </td>
                                <td class="p-2">
                                    {{ $row->horariosalida ? \Carbon\Carbon::parse($row->horariosalida)->format('H:i:s') : '-' }}
                                </td>
                                <td class="p-2">
                                    @if($row->horarioentrada && $row->horariosalida)
                                        {{ \Carbon\Carbon::parse($row->horarioentrada)->diffInHours(\Carbon\Carbon::parse($row->horariosalida)) }} h
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="p-2">
                                    @if(!$row->horarioentrada)
                                        <span class="text-red-600 font-semibold">Sin entrada</span>
                                    @elseif(!$row->horariosalida)
                                        <span class="text-yellow-600 font-semibold">Incompleto</span>
                                    @else
                                        <span class="text-green-600 font-semibold">Completo</span>
                                    @endif
                                </td>
                                <td class="p-2">
                                    <a href="{{ route('horarios.show', $row) }}"
                                       class="text-blue-600 hover:underline">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-4 text-center text-gray-500">No se encontraron registros.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $horarios->withQueryString()->links() }}
                    </div>
                </div>

                @if(request('user_id') && $horasFormateadas)
                    <div class="mt-6 p-4 bg-white rounded-xl shadow text-gray-700">
                        <p class="text-lg">
                            Total de horas trabajadas por <strong>{{ $usuarios->firstWhere('id', request('user_id'))->name }}</strong>
                            entre {{ request('fecha1') }} y {{ request('fecha2') }}:
                            <span class="font-bold text-orange-600">{{ $horasFormateadas }} hs</span>
                        </p>
                    </div>
                @endif

            </div>
        </main>
    </div>

    <!-- Auto-submit filtros al cambiar -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('form select, form input[type="date"]').forEach(el => {
                el.addEventListener('change', () => {
                    el.closest('form').submit();
                });
            });
        });
    </script>
</x-app-layout>

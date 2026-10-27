<x-app-layout>
    <div class="flex">
        <x-sidebar />
        <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-4 md:p-8 transition-all duration-300">

            <div class="max-w-6xl mx-auto">


                <div class="flex items-center justify-between mb-6 mt-4">
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="size-7 text-indigo-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        <h1 class="text-2xl font-bold text-indigo-700">Residencias</h1>
                    </div>
                    <div class="flex items-center gap-2">
                        {{-- Exportar respeta los filtros activos --}}
                        <a href="{{ route('residencia.export', request()->only(['user_id', 'estado'])) }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 text-white rounded-lg font-semibold shadow hover:bg-emerald-700 transition text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            Exportar CSV
                        </a>
                        <a href="{{ route('residencia.card') }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold shadow hover:bg-indigo-700 transition text-sm">
                            Mi residencia
                        </a>
                    </div>
                </div>

                {{-- Filtros --}}
                <form method="GET" action="{{ route('residencia.index') }}"
                      class="bg-white shadow rounded-xl p-4 mb-6 flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Usuario</label>
                        <select name="user_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Todos</option>
                            @foreach($usuarios as $u)
                                <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Estado</label>
                        <select name="estado" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Todos</option>
                            <option value="activa"  @selected(request('estado') === 'activa')>Activa</option>
                            <option value="cerrada" @selected(request('estado') === 'cerrada')>Cerrada</option>
                        </select>
                    </div>
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">
                        Filtrar
                    </button>
                    @if(request()->hasAny(['user_id','estado']))
                        <a href="{{ route('residencia.index') }}"
                           class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-200 transition">
                            Limpiar
                        </a>
                    @endif
                </form>

                {{-- Tabla --}}
                <div class="bg-white shadow rounded-2xl overflow-hidden">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-indigo-50 text-indigo-700 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3">Usuario</th>
                                <th class="px-4 py-3">Ingreso</th>
                                <th class="px-4 py-3">Egreso</th>
                                <th class="px-4 py-3 text-center">Días</th>
                                <th class="px-4 py-3 text-center">Horas</th>
                                <th class="px-4 py-3">Estado</th>
                                <th class="px-4 py-3">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($residencias as $r)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-800">
                                        {{ $r->usuario->name ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">
                                        {{ \Carbon\Carbon::parse($r->horario_ingreso)->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">
                                        @if($r->horario_egreso)
                                            {{ \Carbon\Carbon::parse($r->horario_egreso)->format('d/m/Y H:i') }}
                                        @else
                                            <span class="text-gray-400 italic">Sin egreso</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($r->horario_egreso)
                                            <span class="inline-block bg-indigo-100 text-indigo-700 font-bold px-3 py-1 rounded-full">
                                                {{ $r->dias_transcurridos }}
                                            </span>
                                        @else
                                            @php
                                                $diasActuales = (int) \Carbon\Carbon::parse($r->horario_ingreso)->diffInDays(\Carbon\Carbon::now());
                                            @endphp
                                            <span class="inline-block bg-amber-100 text-amber-700 font-bold px-3 py-1 rounded-full">
                                                {{ $diasActuales }}+
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm">
                                        @if($r->horario_egreso)
                                            <span class="text-indigo-600 font-semibold">{{ $r->horas_transcurridas ?? '—' }}</span>
                                        @else
                                            @php
                                                $minTotales = \Carbon\Carbon::parse($r->horario_ingreso)->diffInMinutes(\Carbon\Carbon::now());
                                                $hAct = (int)($minTotales / 60);
                                                $mAct = $minTotales % 60;
                                            @endphp
                                            <span class="text-amber-600 font-semibold">{{ $hAct }}h {{ $mAct }}m+</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($r->horario_egreso)
                                            <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded-full">Cerrada</span>
                                        @else
                                            <span class="bg-amber-100 text-amber-700 text-xs font-semibold px-2 py-1 rounded-full">Activa</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 text-xs max-w-xs truncate">
                                        {{ $r->observaciones ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-400 italic">
                                        No hay registros de residencia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                @if($residencias->hasPages())
                    <div class="mt-4">
                        {{ $residencias->links() }}
                    </div>
                @endif

            </div>
        </main>
    </div>
</x-app-layout>

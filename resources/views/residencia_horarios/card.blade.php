<x-app-layout>
    <div class="flex">
        <x-sidebar />
        <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-4 md:p-8 flex justify-center">
            <div class="w-full max-w-md">

                <div class="bg-white shadow-lg rounded-2xl p-8 flex flex-col items-center">
                    {{-- Título --}}
                    <div class="flex items-center gap-3 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="size-6 text-indigo-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        <h2 class="text-2xl font-bold text-indigo-700">Residencia</h2>
                    </div>

                    {{-- Alertas de sesión --}}
                    @if(session('success'))
                        <div class="mb-4 w-full bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-center font-semibold">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mb-4 w-full bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-center font-semibold">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- === ESTADO: Sin residencia activa === --}}
                    @if(!$residenciaActiva)
                        <p class="mb-6 text-gray-500 text-sm text-center">No hay residencia activa. Registrá tu ingreso para comenzar.</p>

                        <form method="POST" action="{{ route('residencia.ingreso') }}" class="w-full space-y-4">
                            @csrf

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha y hora de ingreso</label>
                                <input type="datetime-local"
                                       name="horario_ingreso"
                                       value="{{ old('horario_ingreso', now()->format('Y-m-d\TH:i')) }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                       required>
                                @error('horario_ingreso')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones (opcional)</label>
                                <textarea name="observaciones" rows="2"
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                          placeholder="Motivo, destino u otro detalle...">{{ old('observaciones') }}</textarea>
                            </div>

                            <button type="submit"
                                    class="w-full bg-indigo-600 text-white py-3 rounded-xl font-bold hover:bg-indigo-700 transition flex items-center justify-center gap-2 text-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25" />
                                </svg>
                                Registrar ingreso
                            </button>
                        </form>

                    {{-- === ESTADO: Residencia activa (tiene ingreso, falta egreso) === --}}
                    @else
                        <div class="w-full mb-6 bg-indigo-50 rounded-xl p-4 text-center">
                            <span class="block text-xs text-indigo-500 uppercase font-semibold mb-1">Residencia activa desde</span>
                            <span class="block text-xl font-bold text-indigo-800">
                                {{ \Carbon\Carbon::parse($residenciaActiva->horario_ingreso)->format('d/m/Y') }}
                            </span>
                            <span class="block text-sm text-indigo-600 mt-1">
                                {{ \Carbon\Carbon::parse($residenciaActiva->horario_ingreso)->format('H:i') }} hs
                            </span>
                            @if($residenciaActiva->observaciones)
                                <p class="mt-2 text-xs text-gray-500 italic">{{ $residenciaActiva->observaciones }}</p>
                            @endif

                            {{-- Días transcurridos en tiempo real --}}
                            @php
                                $diasActuales = (int) \Carbon\Carbon::parse($residenciaActiva->horario_ingreso)->diffInDays(\Carbon\Carbon::now());
                            @endphp
                            <div class="mt-3 inline-block bg-indigo-100 text-indigo-700 px-4 py-1 rounded-full text-sm font-semibold">
                                {{ $diasActuales }} día(s) en residencia
                            </div>
                        </div>

                        <form method="POST" action="{{ route('residencia.egreso') }}" class="w-full space-y-4">
                            @csrf

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha y hora de egreso</label>
                                <input type="datetime-local"
                                       name="horario_egreso"
                                       value="{{ old('horario_egreso', now()->format('Y-m-d\TH:i')) }}"
                                       min="{{ \Carbon\Carbon::parse($residenciaActiva->horario_ingreso)->format('Y-m-d\TH:i') }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                       required>
                                @error('horario_egreso')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit"
                                    class="w-full bg-amber-500 text-white py-3 rounded-xl font-bold hover:bg-amber-600 transition flex items-center justify-center gap-2 text-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                                </svg>
                                Registrar egreso
                            </button>
                        </form>
                    @endif
                </div>

                {{-- Última residencia cerrada --}}
                @if($ultimaResidencia)
                    <div class="mt-6 bg-white shadow rounded-2xl p-5">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Última residencia cerrada</h3>
                        <div class="flex justify-between text-sm text-gray-700">
                            <div>
                                <span class="block text-xs text-gray-400">Ingreso</span>
                                <span class="font-semibold">{{ \Carbon\Carbon::parse($ultimaResidencia->horario_ingreso)->format('d/m/Y H:i') }}</span>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400">Egreso</span>
                                <span class="font-semibold">{{ \Carbon\Carbon::parse($ultimaResidencia->horario_egreso)->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="text-center">
                                <span class="block text-xs text-gray-400">Días</span>
                                <span class="font-bold text-indigo-700 text-lg">{{ $ultimaResidencia->dias_transcurridos }}</span>
                            </div>
                            <div class="text-center">
                                <span class="block text-xs text-gray-400">Horas</span>
                                <span class="font-bold text-indigo-500 text-sm">{{ $ultimaResidencia->horas_transcurridas ?? '—' }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Botón admin --}}
                @if(auth()->user()->hasRole('admin'))
                    <div class="mt-6 text-center">
                        <a href="{{ route('residencia.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold shadow hover:bg-indigo-700 transition text-sm">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0121 9.414V19a2 2 0 01-2 2z" />
                            </svg>
                            Ver todas las residencias
                        </a>
                    </div>
                @endif

            </div>
        </main>
    </div>
</x-app-layout>

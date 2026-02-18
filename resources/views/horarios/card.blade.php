<x-app-layout>
    <div class="flex">
        <x-sidebar />
        <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-4 md:p-8 flex justify-center">
            <div class="w-full max-w-md">
                <div class="bg-white shadow-lg rounded-2xl p-8 flex flex-col items-center">
                    <div class="flex items-center gap-3 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"  class="size-6 text-orange-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>


                        <h2 class="text-2xl font-bold text-orange-700">Registro de horario</h2>
                    </div>
                    <p class="mb-6 text-gray-700">
                        Fecha: <span class="font-semibold">{{ \Carbon\Carbon::parse($hoy)->format('d/m/Y') }}</span>
                    </p>
                    @if(!$horario)
                        <!-- No hay registro de entrada hoy -->
                        <form method="POST" action="{{ route('horario.entrada') }}" class="w-full flex flex-col items-center">
                            @csrf
                            <button type="submit" class="bg-orange-600 text-white w-full py-3 rounded-xl font-bold hover:bg-orange-700 transition flex items-center justify-center gap-2 text-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-white" >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 2.994v2.25m10.5-2.25v2.25m-14.252 13.5V7.491a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v11.251m-18 0a2.25 2.25 0 0 0 2.25 2.25h13.5a2.25 2.25 0 0 0 2.25-2.25m-18 0v-7.5a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v7.5m-6.75-6h2.25m-9 2.25h4.5m.002-2.25h.005v.006H12v-.006Zm-.001 4.5h.006v.006h-.006v-.005Zm-2.25.001h.005v.006H9.75v-.006Zm-2.25 0h.005v.005h-.006v-.005Zm6.75-2.247h.005v.005h-.005v-.005Zm0 2.247h.006v.006h-.006v-.006Zm2.25-2.248h.006V15H16.5v-.005Z" />
                        </svg>
                                Registrar horario de ingreso
                            </button>
                        </form>
                    @elseif(!$horario->horariosalida)
                        <!-- Ya cargó entrada, falta salida -->
                        <div class="mb-6 w-full text-center">
                            <span class="block mb-1 text-gray-500">Hora de ingreso:</span>
                            <span class="block text-lg font-bold text-gray-900">
                                {{ \Carbon\Carbon::parse($horario->horarioentrada)->format('H:i:s') }}
                            </span>
                        </div>
                        <form method="POST" action="{{ route('horario.salida') }}" class="w-full flex flex-col items-center">
                            @csrf
                            <button type="submit" class="bg-yellow-500 text-white w-full py-3 rounded-xl font-bold hover:bg-yellow-600 transition flex items-center justify-center gap-2 text-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                </svg>

                                Registrar horario de salida
                            </button>
                        </form>
                    @else
                        <!-- Ya cargó entrada y salida -->
                        <div class="mb-4 w-full text-center">
                            <span class="block text-gray-500">Hora de ingreso:</span>
                            <span class="block text-lg font-bold text-gray-900 mb-2">
                                {{ \Carbon\Carbon::parse($horario->horarioentrada)->format('H:i:s') }}
                            </span>
                            <span class="block text-gray-500">Hora de salida:</span>
                            <span class="block text-lg font-bold text-gray-900">
                                {{ \Carbon\Carbon::parse($horario->horariosalida)->format('H:i:s') }}
                            </span>
                        </div>
                        <div class="bg-orange-100 text-orange-700 px-4 py-2 rounded-lg font-semibold text-center">
                            ¡Tus horarios del día quedaron cargados!
                        </div>
                    @endif
                    @if(session('success'))
                        <div class="mt-6 text-orange-600 font-bold text-center">
                            {{ session('success') }}
                        </div>
                    @endif
                </div>

                 <div class="w-full max-w-md mx-auto mt-8 justify-center text-center">
                    @if(auth()->user()->hasRole('admin'))
                        <div class="mb-6">
                            <a href="{{ route('horarios.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-md font-semibold shadow hover:bg-orange-700 transition">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                Ver y Exportar Jornales
                            </a>
                        </div>
                    @endif
                 </div>
            </div>
        </main>
    </div>
</x-app-layout>

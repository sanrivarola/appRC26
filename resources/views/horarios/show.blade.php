<x-app-layout>
    <div class="flex">
        <x-sidebar />

        <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-6">
            <div class="max-w-3xl mx-auto bg-white shadow rounded-2xl p-6">

                <h1 class="text-2xl font-bold text-orange-600 mb-6 flex items-center gap-2">
                    <svg class="h-7 w-7 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Detalle del Horario
                </h1>

                <div class="space-y-4 text-gray-800 text-md">
                    <div><strong>Equipo:</strong> {{ $horario->equipo }}</div>
                    <div><strong>Fecha:</strong> {{ $horario->dia }}</div>
                    <div><strong>Hora de entrada:</strong> {{ $horario->horarioentrada ? \Carbon\Carbon::parse($horario->horarioentrada)->format('H:i:s') : '-' }}</div>
                    <div><strong>Hora de salida:</strong> {{ $horario->horariosalida ? \Carbon\Carbon::parse($horario->horariosalida)->format('H:i:s') : '-' }}</div>
                </div>

                <div class="mt-8">
                    <a href="{{ route('horarios.index') }}" class="text-orange-600 hover:underline">&larr; Volver al listado</a>
                </div>

            </div>
        </main>
    </div>
</x-app-layout>

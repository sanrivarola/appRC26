<x-app-layout>
    <div class="flex">
        {{-- Sidebar global --}}
        <x-sidebar />

        {{-- Contenido principal --}}
        <main class="flex-1 bg-slate-100 min-h-screen md:ml-64 p-6 md:p-10">

            <div class="max-w-4xl mx-auto backdrop-blur-md bg-white/80  shadow-xl rounded-3xl p-8">

                {{-- Encabezado --}}
                <h1 class="text-2xl font-extrabold text-indigo-700 dark:text-indigo-400 flex items-center gap-3 mb-8">
                    <i class="fa-solid fa-clock-rotate-left fa-lg"></i>
                    Devoluciones de:<span class="truncate">{{ $tarea->tarea }}</span>
                </h1>

                {{-- Tabla de devoluciones --}}
                @if ($devoluciones->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm border">
                            <thead class="bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-900">
                                <tr>
                                    <th class="p-3 text-left font-semibold">Equipo</th>
                                    <th class="p-3 text-left font-semibold">Fecha</th>
                                    <th class="p-3 text-left font-semibold">¿Finalizada?</th>
                                    <th class="p-3 text-left font-semibold">Comentario</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                @foreach ($devoluciones as $dev)
                                    <tr class="hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                                        <td class="p-3 font-medium">{{ $dev->equipo }}</td>
                                        <td class="p-3">
                                            {{ \Carbon\Carbon::parse($dev->fecha)->format('d/m/Y') }}
                                        </td>
                                        <td class="p-3">
                                            @if ($dev->finalizada === 'si')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-green-200 text-green-700 text-xs">
                                                    <i class="fa-solid fa-check"></i> Sí
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-200 text-red-700 text-xs">
                                                    <i class="fa-solid fa-xmark"></i> No
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-3 whitespace-pre-line">
                                            {{ $dev->devolucion ?? '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="italic text-slate-500 dark:text-slate-400">
                        No hay devoluciones registradas.
                    </p>
                @endif

                {{-- Botón volver --}}
                <div class="mt-8 flex justify-end">
                    <a href="{{ route('tareas.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-slate-600 text-white rounded shadow hover:bg-slate-700 transition">
                        <i class="fa-solid fa-arrow-left"></i> Volver a tareas
                    </a>
                </div>

            </div>
        </main>
    </div>
</x-app-layout>

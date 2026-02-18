{{-- resources/views/tareas/show.blade.php --}}
<x-app-layout>
    <div class="flex">
        <x-sidebar />

        <main class="flex-1 bg-slate-100 min-h-screen md:ml-64 p-6 md:p-10">
            <!-- CARD PRINCIPAL -->
            <div class="max-w-4xl mx-auto backdrop-blur-md bg-white/80 shadow-xl rounded-3xl p-8">

                <!-- ENCABEZADO -->
                <div class="flex items-center gap-3 mb-6">
                <h1 class="flex-1 flex items-center gap-3 text-3xl font-extrabold text-green-700 dark:text-green-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-700">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" />
                    </svg>

                    {{ $tarea->tarea }}

                    <span @class([
                            'ml-auto inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full',
                            'bg-red-200 text-red-700'     => $tarea->prioridad === 'Alta',
                            'bg-yellow-200 text-yellow-700' => $tarea->prioridad === 'Media',
                            'bg-green-200 text-green-700' => $tarea->prioridad === 'Baja',
                        ])>
                    <i class="fa-solid fa-flag"></i> {{ $tarea->prioridad }}
                    </span>
                </h1>

                {{-- Botón “Volver” compacto --}}
                <a href="{{ url()->previous() }}"
                    class="inline-flex items-center gap-1 text-sm px-3 py-1.5
                            bg-slate-500 text-white rounded hover:bg-slate-600 transition">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </a>
                </div>


                <!-- DESCRIPCIÓN -->
                @if ($tarea->detalle)
                    <div class="prose dark:prose-invert max-w-none mb-8 border border-gray-300 bg-gray-100 p-4 rounded-lg">
                        {!! nl2br(e($tarea->detalle)) !!}
                    </div>
                @endif

                <!-- GRID INFO RÁPIDA -->
                <div class="grid gap-4 md:grid-cols-2 mb-8">
                    <x-info-box label="Responsable">
                        {{ $tarea->equipo }}
                    </x-info-box>

                    <x-info-box label="Fecha de carga">
                        {{ \Carbon\Carbon::parse($tarea->fecha_carga)->format('d/m/Y') }}
                    </x-info-box>

                    <x-info-box label="Horario">
                        @if ($tarea->horacomienzo)
                            {{ $tarea->horacomienzo }}&nbsp;–&nbsp;{{ $tarea->horafinal }}
                        @else
                            — 
                        @endif
                    </x-info-box>

                    <x-info-box label="Estado">
                        <span class="inline-flex items-center gap-1 text-sm font-semibold px-2 py-0.5 rounded-full
                            @if($tarea->estado==1) bg-green-200 text-green-700
                            @elseif($tarea->estado==2) bg-purple-200 text-purple-700
                            @else bg-yellow-200 text-yellow-700 @endif
                        ">
                            @if($tarea->estado==1)
                                <i class="fa-solid fa-toggle-on"></i> Activa
                            @elseif($tarea->estado==2)
                                <i class="fa-solid fa-pause"></i> Pausada
                            @else
                                <i class="fa-solid fa-archive"></i> Archivada
                            @endif
                        </span>
                    </x-info-box>
                </div>

                <!-- DÍAS -->
                <div class="mb-8">
                    <h2 class="font-semibold mb-2">Días de ejecución</h2>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $diasArr = ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'];
                        @endphp
                        @foreach ($diasArr as $d)
                            @if ($tarea->$d === 'si')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-600/10 text-indigo-700">
                                    {{ strtoupper(substr($d,0,3)) }}
                                </span>
                            @endif
                        @endforeach

                        @if ($tarea->norepite === 'si')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-pink-600/10 text-pink-700">
                                ÚNICA VEZ
                            </span>
                        @endif
                    </div>
                </div>

                <!-- LINKS -->
                @if ($tarea->link1 || $tarea->link2 || $tarea->link3)
                    <div class="mb-8">
                        <h2 class="font-semibold mb-2">Adjuntos / enlaces</h2>
                        <ul class="space-y-1">
                            @foreach([1,2,3] as $n)
                                @php $ln='link'.$n; @endphp
                                @if ($tarea->$ln)
                                    <li>
                                        <a href="{{ $tarea->$ln }}" target="_blank"
                                           class="inline-flex items-center gap-2 text-indigo-600 hover:underline">
                                            <i class="fa-solid fa-link"></i> {{ Str::limit($tarea->$ln, 60) }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- FORMULARIO DEVOLUCIÓN --}}
                @role('colaborador')
                    <form action="{{ route('tareas.devoluciones.guardar', $tarea->id_tarea) }}"
                            method="POST" class="space-y-4">
                        @csrf

                        {{-- Realizada / No realizada --}}
                        <div class="flex gap-4">
                            <label><input type="radio" name="finalizada" value="si"> Realizada</label>
                            <label><input type="radio" name="finalizada" value="no" checked> No realizada</label>
                        </div>

                        {{-- Comentario opcional --}}
                        <textarea name="devolucion" rows="3"
                                    class="w-full border rounded p-2"
                                    placeholder="Comentario…"></textarea>

                        {{-- Equipo responsable (usuario actual) --}}
                        <input type="hidden" name="equipo" value="{{ auth()->user()->name }}">

                        <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                            Guardar
                        </button>
                        </form>
                  {{-- Devoluciones del día para el colaborador --}}
                @if($devoluciones->count())
                    <h2 class="text-lg font-semibold mt-6 flex items-center gap-2">
                        <i class="fa-solid fa-list"></i> Mis devoluciones de hoy
                    </h2>
                    <ul class="space-y-3">
                        @foreach($devoluciones as $dev)
                        <li class="flex justify-between items-start bg-green-50 border border-green-200 rounded px-3 py-1">
                            <div class="flex-1">
                            <span class="font-medium">
                                {{ $dev->finalizada==='si' ? '✔' : '✖' }}
                                {{ Str::limit($dev->devolucion, 60,'…') ?: '— sin comentario —' }}
                            </span>
                            <time class="ml-2 text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($dev->fecha)->format('H:i') }}
                            </time>
                            </div>

                            {{-- Botón borrar --}}
                            <form action="{{ route('tareas.devoluciones.destroy', [$tarea->id_tarea,$dev->id]) }}"
                                method="POST"
                                onsubmit="return confirm('¿Eliminar esta devolución?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:text-red-800 text-xs">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            </form>
                        </li>
                        @endforeach
                    </ul>
                    @endif

                @else
                   {{-- HISTORIAL - admin/editor --}}
                        @role('admin|editor')
                        <h2 class="text-lg font-semibold text-indigo-700 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-clock-rotate-left"></i> Historial de devoluciones
                        </h2>

                        @if($devoluciones->count())
                            <div class="bg-white/70 border border-indigo-200 rounded-lg overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-indigo-50 text-indigo-700">
                                <tr>
                                    <th class="px-3 py-1 text-left">Fecha</th>
                                    <th class="px-3 py-1 text-left">Equipo</th>
                                    <th class="px-3 py-1 text-left">Estado</th>
                                    <th class="px-3 py-1 text-left">Comentario</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($devoluciones as $dev)
                                    <tr class="odd:bg-indigo-50/20 hover:bg-indigo-100/40">
                                    <td class="px-3 py-1 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($dev->fecha)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-3 py-1">{{ $dev->equipo }}</td>
                                    <td class="px-3 py-1">
                                        @if($dev->finalizada==='si')
                                        <span class="text-green-600">✔ Realizada</span>
                                        @else
                                        <span class="text-red-600">✖ No</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-1">
                                        {{ Str::limit($dev->devolucion,60) }}
                                    </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            </div>

                            {{-- Paginación compacta --}}
                            <div class="mt-4">
                            {{ $devoluciones->onEachSide(0)->links() }}
                            </div>
                        @else
                            <p class="italic text-slate-500">Aún no hay devoluciones.</p>
                        @endif
                        @endrole
                    @endif
            </div>
        </main>
    </div>
</x-app-layout>

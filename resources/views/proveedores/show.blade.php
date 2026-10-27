{{-- resources/views/proveedores/show.blade.php --}}
<x-app-layout>
    <div class="flex">
        <x-sidebar />

        <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-4 md:p-8">
            <div class="max-w-4xl mx-auto">

                <div class="bg-white shadow rounded-2xl p-8 space-y-6">

                    <h2 class="text-3xl font-semibold text-violet-700 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>
                        Detalle proveedor
                    </h2>

                    <dl class="divide-y divide-gray-200 text-sm">
                        <div class="py-3 grid grid-cols-3 gap-4">
                            <dt class="font-medium text-violet-500">Área</dt>
                            <dd class="col-span-2 text-gray-800">{{ $proveedor->area }}</dd>
                        </div>

                        <div class="py-3 grid grid-cols-3 gap-4">
                            <dt class="font-medium text-violet-500">Nombre</dt>
                            <dd class="col-span-2 text-gray-800">{{ $proveedor->nombre }}</dd>
                        </div>

                        <div class="py-3 grid grid-cols-3 gap-4">
                            <dt class="font-medium text-violet-500">Datos de contacto</dt>
                            <dd class="col-span-2 text-gray-800 whitespace-pre-line">
                                {{ $proveedor->datos }}
                            </dd>
                        </div>

                        <div class="py-3 grid grid-cols-3 gap-4">
                            <dt class="font-medium text-violet-500">Producto / Servicio</dt>
                            <dd class="col-span-2 text-gray-800 whitespace-pre-line">
                                {{ $proveedor->producto }}
                            </dd>
                        </div>

                        <div class="py-3 grid grid-cols-3 gap-4">
                            <dt class="font-medium text-violet-500">Responsable interno</dt>
                            <dd class="col-span-2 text-gray-800">{{ $proveedor->responsable }}</dd>
                        </div>

                        <div class="py-3 grid grid-cols-3 gap-4">
                            <dt class="font-medium text-violet-500">Detalle</dt>
                            <dd class="col-span-2 text-gray-800 whitespace-pre-line">
                                {{ $proveedor->detalle }}
                            </dd>
                        </div>

                        <div class="py-3 grid grid-cols-3 gap-4">
                            <dt class="font-medium text-violet-500">Cargado por</dt>
                            <dd class="col-span-2 text-gray-800">
                                {{ $proveedor->creador }} &nbsp;|&nbsp;
                                {{ \Carbon\Carbon::parse($proveedor->fechacarga)->format('d/m/Y') }}
                            </dd>
                        </div>
                    </dl>


                    {{-- Botones ---------------------------------------------------------}}
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('proveedores.edit', $proveedor) }}"
                           class="inline-flex items-center gap-1 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded transition">
                            <i class="fa-solid fa-pen-to-square"></i> Editar
                        </a>

                        <a href="{{ route('tareas-proveedores.create', ['proveedor_id' => $proveedor->id_proveedores]) }}"
                           class="inline-flex items-center gap-1 bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded transition">
                            <i class="fa-solid fa-bell"></i> Nueva tarea
                        </a>

                        <form action="{{ route('proveedores.destroy', $proveedor) }}"
                              method="POST"
                              onsubmit="return confirm('¿Borrar este proveedor?')" >
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition">
                                <i class="fa-solid fa-trash-can"></i> Borrar
                            </button>
                        </form>

                        <a href="{{ route('proveedores.index') }}"
                           class="ml-auto text-violet-700 hover:text-violet-900 font-semibold">
                            ← Volver al listado
                        </a>
                    </div>
                </div>

                {{-- DASHBOARD TAREAS --}}
                @php
                    $tareas        = $proveedor->tareasProveedores()->latest()->get();
                    $totalTareas   = $tareas->count();
                    $pendientes    = $tareas->where('estado','Pendiente')->count();
                    $enCurso       = $tareas->where('estado','En curso')->count();
                    $cerradas      = $tareas->where('estado','Cerrada')->count();
                    $promedio      = $tareas->whereNotNull('calificacion')->avg('calificacion');
                    $legacyTareas  = \DB::table('tareas_proveedores')->where('proveedor', $proveedor->nombre)->orderByDesc('fecha')->get();
                @endphp

                @if($totalTareas > 0 || $legacyTareas->count() > 0)
                <div class="bg-white shadow rounded-2xl p-6 mt-6 space-y-6">

                    {{-- Mini stats --}}
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <h3 class="text-lg font-bold text-slate-700 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-sky-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
                            </svg>
                            Tareas
                        </h3>
                        <a href="{{ route('tareas-proveedores.index', ['proveedor_id' => $proveedor->id_proveedores]) }}"
                           class="text-sm text-sky-600 hover:underline">Ver todas →</a>
                    </div>

                    {{-- Tarjetas stat --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-center text-sm">
                        <div class="bg-slate-50 rounded-xl p-3">
                            <div class="text-2xl font-bold text-slate-800">{{ $totalTareas }}</div>
                            <div class="text-xs text-slate-400 mt-1">Total</div>
                        </div>
                        <div class="bg-yellow-50 rounded-xl p-3">
                            <div class="text-2xl font-bold text-yellow-700">{{ $pendientes }}</div>
                            <div class="text-xs text-yellow-600 mt-1">Pendientes</div>
                        </div>
                        <div class="bg-green-50 rounded-xl p-3">
                            <div class="text-2xl font-bold text-green-700">{{ $cerradas }}</div>
                            <div class="text-xs text-green-600 mt-1">Cerradas</div>
                        </div>
                        <div class="bg-amber-50 rounded-xl p-3">
                            @if($promedio)
                                <div class="flex justify-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                             class="size-4 {{ $i <= round($promedio) ? 'fill-amber-400' : 'fill-slate-200' }}">
                                            <path d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/>
                                        </svg>
                                    @endfor
                                </div>
                                <div class="text-xs text-amber-600 mt-1">{{ number_format($promedio,1) }}/5 promedio</div>
                            @else
                                <div class="text-2xl font-bold text-slate-300">—</div>
                                <div class="text-xs text-slate-400 mt-1">Sin calificaciones</div>
                            @endif
                        </div>
                    </div>

                    {{-- Últimas tareas actuales --}}
                    @if($totalTareas > 0)
                    <div class="space-y-2">
                        @foreach($tareas->take(5) as $t)
                            <a href="{{ route('tareas-proveedores.show', $t) }}"
                               class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition group">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs px-2 py-1 rounded-full font-medium
                                        {{ $t->estado === 'Pendiente' ? 'bg-yellow-100 text-yellow-700' : ($t->estado === 'En curso' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500') }}">
                                        {{ $t->estado }}
                                    </span>
                                    <span class="text-sm text-slate-700 group-hover:text-sky-700 font-medium">{{ \Illuminate\Support\Str::limit($t->titulo, 50) }}</span>
                                </div>
                                <span class="text-xs text-slate-400 shrink-0">{{ optional($t->fecha_carga)->format('d/m/Y') }}</span>
                            </a>
                        @endforeach
                    </div>
                    @endif

                    {{-- Historial legacy --}}
                    @if($legacyTareas->count() > 0)
                    <div class="border-t pt-4">
                        <h4 class="text-sm font-semibold text-slate-500 mb-3 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                            Historial previo al sistema
                        </h4>
                        <div class="space-y-2">
                            @foreach($legacyTareas as $l)
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-sm">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="font-medium text-slate-600">{{ $l->creador }}</span>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs px-2 py-0.5 rounded-full
                                                {{ $l->realizada === 'Si' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $l->realizada === 'Si' ? 'Realizada' : 'No realizada' }}
                                            </span>
                                            <span class="text-xs text-slate-400">{{ $l->fecha }}</span>
                                        </div>
                                    </div>
                                    <p class="text-slate-600 text-xs leading-relaxed line-clamp-3">{{ \Illuminate\Support\Str::limit($l->tarea, 200) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
                @else
                <div class="bg-white shadow rounded-2xl p-6 mt-6">
                    <div class="flex items-center justify-between">
                        <p class="text-slate-400 text-sm">Sin tareas registradas para este proveedor.</p>
                        <a href="{{ route('tareas-proveedores.create', ['proveedor_id' => $proveedor->id_proveedores]) }}"
                           class="text-sm bg-sky-600 hover:bg-sky-700 text-white px-3 py-1.5 rounded-lg font-medium transition">
                            + Crear primera tarea
                        </a>
                    </div>
                </div>
                @endif

            </div>
        </main>
    </div>
</x-app-layout>
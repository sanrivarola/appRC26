{{-- resources/views/tareas/export.blade.php --}}
<x-app-layout>
    <div class="flex">
        <x-sidebar />

        <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-6">
            <div class="max-w-5xl mx-auto bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Exportar Devoluciones</h2>

                {{-- Formulario (tu mismo form; ojo action GET para previsualizar, y POST para exportar) --}}
                <form method="GET" action="{{ route('tareas.export.form') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    @csrf
                    <div>
                        <label for="fecha_desde" class="block text-sm font-medium text-gray-700">Fecha desde</label>
                        <input type="date" name="fecha_desde" id="fecha_desde" value="{{ request('fecha_desde') }}" class="mt-1 block w-full border-gray-300 rounded-md" required>
                        @error('fecha_desde') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="fecha_hasta" class="block text-sm font-medium text-gray-700">Fecha hasta</label>
                        <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ request('fecha_hasta') }}" class="mt-1 block w-full border-gray-300 rounded-md" required>
                        @error('fecha_hasta') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="equipo" class="block text-sm font-medium text-gray-700">Responsable</label>
                        <select name="equipo" id="equipo" class="mt-1 block w-full border-gray-300 rounded-md">
                            <option value="todos">— Todos —</option>
                            @foreach($equipos as $eq)
                                <option value="{{ $eq }}" {{ request('equipo') === $eq ? 'selected' : '' }}>{{ $eq }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded-md">Previsualizar</button>
                    </div>
                </form>

                {{-- Si hay preview, mostramos tabla + botón de export --}}
                @if($preview)
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm text-gray-600">
                            Mostrando {{ $preview->firstItem() }}–{{ $preview->lastItem() }} de {{ $total }} registros
                        </p>

                        <form method="POST" action="{{ route('tareas.export') }}">
                            @csrf
                            <input type="hidden" name="fecha_desde" value="{{ request('fecha_desde') }}">
                            <input type="hidden" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
                            <input type="hidden" name="equipo" value="{{ request('equipo', 'todos') }}">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-md">Descargar Excel</button>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-gray-100 text-left">
                                    <th class="py-2 px-3">Fecha</th>
                                    <th class="py-2 px-3">Equipo</th>
                                    <th class="py-2 px-3">ID Tarea</th>
                                    <th class="py-2 px-3">Tarea</th>
                                    <th class="py-2 px-3">Finalizada</th>
                                    <th class="py-2 px-3">Devolución</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($preview as $r)
                                    <tr class="border-b">
                                        <td class="py-2 px-3">{{ $r->fecha }}</td>
                                        <td class="py-2 px-3">{{ $r->equipo }}</td>
                                        <td class="py-2 px-3">{{ $r->id_tarea }}</td>
                                        <td class="py-2 px-3">{{ $r->nombre_tarea }}</td>
                                        <td class="py-2 px-3">{{ $r->finalizada }}</td>
                                        <td class="py-2 px-3">
                                            {{ is_string($r->devolucion) ? Str::limit($r->devolucion, 120) : json_encode($r->devolucion) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $preview->links() }}
                    </div>
                @endif

                <a href="{{ route('tareas.index') }}" class="inline-block mt-6 text-gray-600 hover:underline">← Volver al panel de tareas</a>
            </div>
        </main>
    </div>
</x-app-layout>

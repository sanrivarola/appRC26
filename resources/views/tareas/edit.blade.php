<x-app-layout>
    <div class="flex">
        <x-sidebar />

        <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-6 md:p-10">
            <div class="max-w-4xl mx-auto bg-white shadow rounded-2xl p-6">
                <h1 class="text-2xl font-semibold text-indigo-700 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square"></i>
                    Editar tarea
                </h1>

                {{-- errores --}}
                @if ($errors->any())
                    <div class="mb-4 text-red-600">
                        <ul class="list-disc pl-6 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('tareas.update', $tarea->id_tarea) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Título --}}
                    <div>
                        <label class="block font-medium mb-1">Título de la tarea</label>
                        <input name="tarea" class="w-full border rounded p-2"
                               value="{{ old('tarea', $tarea->tarea) }}" required>
                    </div>

                    {{-- Responsable + Prioridad --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium mb-1">Responsable</label>
                            <select name="equipo" class="w-full border rounded p-2" required>
                                @foreach($responsables as $person)
                                    <option value="{{ $person }}"
                                        @selected(old('equipo', $tarea->equipo)==$person)>{{ $person }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium mb-1">Prioridad</label>
                            <select name="prioridad" class="w-full border rounded p-2" required>
                                @foreach(['Alta','Media','Baja'] as $prio)
                                    <option value="{{ $prio }}"
                                        @selected(old('prioridad', $tarea->prioridad)==$prio)>{{ $prio }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Detalle --}}
                    <div>
                        <label class="block font-medium mb-1">Detalle</label>
                        <textarea name="detalle" rows="5" class="w-full border rounded p-2" required>
{{ old('detalle', $tarea->detalle) }}</textarea>
                    </div>

                    {{-- Días de la semana --}}
                    <div>
                        <label class="block font-medium mb-2">Días de la semana</label>
                        <div class="flex flex-wrap gap-3">
                            @foreach(['lunes','martes','miercoles','jueves','viernes','sabado','domingo'] as $dia)
                                <label class="inline-flex items-center gap-1">
                                    <input type="checkbox" name="{{ $dia }}" value="si"
                                           @checked(old($dia, $tarea->$dia)=='si')>
                                    <span class="text-sm capitalize">{{ $dia }}</span>
                                </label>
                            @endforeach
                            <label class="inline-flex items-center gap-1">
                                <input type="checkbox" name="norepite" value="si"
                                       @checked(old('norepite', $tarea->norepite)=='si')>
                                <span class="text-sm">(Única vez)</span>
                            </label>
                        </div>
                    </div>

                    {{-- Horario --}}
                    <div>
                        <label class="block font-medium mb-2">Horario</label>
                        <div class="grid grid-cols-3 gap-2 items-center">
                            <select name="horacomienzo" class="border rounded p-2">
                                <option value="">Hora inicio</option>
                                @foreach(range(8,16) as $h)
                                    <option value="{{ $h }}"
                                        @selected(old('horacomienzo', $tarea->horacomienzo)==$h)>{{ $h }}</option>
                                @endforeach
                            </select>
                            <span class="text-center">a</span>
                            <select name="horafinal" class="border rounded p-2">
                                <option value="">Hora fin</option>
                                @foreach(range(9,17) as $h)
                                    <option value="{{ $h }}"
                                        @selected(old('horafinal', $tarea->horafinal)==$h)>{{ $h }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Links --}}
                    @foreach([1,2,3] as $n)
                        <div>
                            <label class="block font-medium mb-1">Link archivo {{ $n }}</label>
                            <input name="link{{ $n }}" class="w-full border rounded p-2" placeholder="https://"
                                   value="{{ old('link'.$n, $tarea->{'link'.$n}) }}">
                        </div>
                    @endforeach

                    {{-- Estado --}}
                    <div>
                        <label class="block font-medium mb-1">Estado</label>
                        <select name="estado" class="w-full border rounded p-2" required>
                            @foreach([1=>'Activa',2=>'Pausada',3=>'Archivada'] as $val=>$label)
                                <option value="{{ $val }}"
                                    @selected(old('estado', $tarea->estado)==$val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Botones --}}
                    <div class="flex justify-between">
                        <a href="{{ route('tareas.index') }}"
                           class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancelar</a>
                        <button type="submit"
                                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                            Actualizar tarea
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</x-app-layout>

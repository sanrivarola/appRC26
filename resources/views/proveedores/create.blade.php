{{-- resources/views/proveedores/create.blade.php --}}
<x-app-layout>
    <div class="flex">
        <x-sidebar />

        <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-4 md:p-8">
            <div class="max-w-3xl mx-auto">

                {{-- Encabezado --}}
                <div class="bg-white shadow rounded-2xl p-6 mb-8">
                    <h2 class="text-2xl font-semibold text-violet-700 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>
                        Nuevo proveedor
                    </h2>
                    <p class="text-gray-500 mt-1">
                        Completa el formulario para agregar un proveedor al sistema.
                    </p>
                </div>

                {{-- Formulario --}}
                <div class="bg-white shadow rounded-2xl p-6">
                    <form action="{{ route('proveedores.store') }}" method="POST" class="space-y-6">
                        @csrf

                        {{-- Área (select dinámico) --}}
                        <div>
                            <label for="area" class="block text-sm font-medium text-gray-700">
                                Área <span class="text-red-600">*</span>
                            </label>
                            <select id="area" name="area"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    required>
                                <option value="" disabled selected>— Selecciona un área —</option>
                                @foreach($areas as $a)
                                    <option value="{{ $a }}" {{ old('area') === $a ? 'selected' : '' }}>
                                        {{ $a }}
                                    </option>
                                @endforeach
                            </select>
                            @error('area')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nombre --}}
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700">
                                Nombre <span class="text-red-600">*</span>
                            </label>
                            <input type="text" id="nombre" name="nombre"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                   value="{{ old('nombre') }}" required>
                            @error('nombre')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Datos de contacto --}}
                        <div>
                            <label for="datos" class="block text-sm font-medium text-gray-700">
                                Datos de contacto <span class="text-red-600">*</span>
                            </label>
                            <textarea id="datos" name="datos" rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                      required>{{ old('datos') }}</textarea>
                            @error('datos')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Producto / servicio --}}
                        <div>
                            <label for="producto" class="block text-sm font-medium text-gray-700">
                                Producto / servicio
                            </label>
                            <textarea id="producto" name="producto" rows="2"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('producto') }}</textarea>
                            @error('producto')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Responsable interno --}}
                        <div>
                            <label for="responsable" class="block text-sm font-medium text-gray-700">
                                Responsable interno <span class="text-red-600">*</span>
                            </label>
                            <input type="text" id="responsable" name="responsable"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                   value="{{ old('responsable') }}" required>
                            @error('responsable')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Detalle --}}
                        <div>
                            <label for="detalle" class="block text-sm font-medium text-gray-700">
                                Detalle <span class="text-red-600">*</span>
                            </label>
                            <textarea id="detalle" name="detalle" rows="4"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                      required>{{ old('detalle') }}</textarea>
                            @error('detalle')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Botones --}}
                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('proveedores.index') }}"
                               class="text-sm text-gray-600 hover:text-gray-800">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-5 py-2 bg-violet-700 hover:bg-violet-600
                                           text-white font-semibold rounded-md shadow-sm transition">
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </main>
    </div>
</x-app-layout>

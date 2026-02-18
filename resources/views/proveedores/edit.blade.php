{{-- resources/views/proveedores/edit.blade.php --}}
<x-app-layout>
    <div class="flex">
        <x-sidebar />

        <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-4 md:p-8">
            <div class="max-w-3xl mx-auto">

                <div class="bg-white shadow rounded-2xl p-8 space-y-6">

                    <h2 class="text-2xl font-semibold text-violet-700 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>
                        Editar proveedor
                    </h2>

                    {{-- MENSAJE FLASH (éxito) --}}
                    @if(session('success'))
                        <div class="bg-emerald-50 border border-emerald-300 text-emerald-700 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- FORMULARIO --}}
                    <form  method="POST"
                           action="{{ route('proveedores.update', $proveedor->id_proveedores) }}">
                        @csrf
                        @method('PUT')

                        {{-- Área --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-violet-500 mb-1">
                                Área <span class="text-red-600">*</span>
                            </label>
                            <select name="area"
                                    class="w-full border rounded px-3 py-2 @error('area') border-red-500 @enderror">
                                <option value="">-- Seleccionar área --</option>
                                @foreach($areas as $a)
                                    <option value="{{ $a }}"
                                        @selected(old('area', $proveedor->area) == $a)>
                                        {{ $a }}
                                    </option>
                                @endforeach
                            </select>
                            @error('area') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Nombre --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-violet-500 mb-1">
                                Nombre <span class="text-red-600">*</span>
                            </label>
                            <input type="text" name="nombre"
                                   value="{{ old('nombre', $proveedor->nombre) }}"
                                   class="w-full border rounded px-3 py-2 @error('nombre') border-red-500 @enderror">
                            @error('nombre') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Responsable --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-violet-500 mb-1">
                                Responsable <span class="text-red-600">*</span>
                            </label>
                            <input type="text" name="responsable"
                                   value="{{ old('responsable', $proveedor->responsable) }}"
                                   class="w-full border rounded px-3 py-2 @error('responsable') border-red-500 @enderror">
                            @error('responsable') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Datos --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-violet-500 mb-1">
                                Datos de contacto <span class="text-red-600">*</span>
                            </label>
                            <textarea name="datos" rows="3"
                                      class="w-full border rounded px-3 py-2 @error('datos') border-red-500 @enderror">{{ old('datos', $proveedor->datos) }}</textarea>
                            @error('datos') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Producto --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-violet-500 mb-1">
                                Producto / Servicio
                            </label>
                            <textarea name="producto" rows="2"
                                      class="w-full border rounded px-3 py-2">{{ old('producto', $proveedor->producto) }}</textarea>
                        </div>

                        {{-- Detalle --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-violet-500 mb-1">
                                Detalle <span class="text-red-600">*</span>
                            </label>
                            <textarea name="detalle" rows="4"
                                      class="w-full border rounded px-3 py-2 @error('detalle') border-red-500 @enderror">{{ old('detalle', $proveedor->detalle) }}</textarea>
                            @error('detalle') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- BOTONES --}}
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('proveedores.index') }}"
                               class="px-4 py-2 bg-gray-200 text-violet-500 rounded hover:bg-gray-300">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="px-5 py-2 bg-violet-700 hover:bg-violet-600 text-white font-semibold rounded">
                                Guardar cambios
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </main>
    </div>
</x-app-layout>

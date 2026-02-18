<x-app-layout>
    <div class="flex">

        <x-sidebar />

        <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-6 md:p-10">
            <div class="max-w-3xl mx-auto bg-white shadow rounded-2xl p-8">

                <h2 class="text-2xl font-bold text-emerald-700 mb-6 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 7h2l2-3h10l2 3h2a2 2 0 012 2v9a2 2 0 01-2 2H3a2 2 0 01-2-2V9a2 2 0 012-2z"/>
                        <circle cx="12" cy="13" r="4"/>
                    </svg>
                    Nuevo relevamiento
                </h2>

                <form action="{{ route('relevamientos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    {{-- Título --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                        <input type="text" name="titulorelevamiento" value="{{ old('titulorelevamiento') }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                               required>
                        @error('titulorelevamiento') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Detalle --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Detalle</label>
                        <textarea name="detallerelevamiento" rows="5"
                                  class="w-full border-gray-300 rounded-lg shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                  required>{{ old('detallerelevamiento') }}</textarea>
                        @error('detallerelevamiento') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Foto --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Foto (opcional)</label>
                        <input type="file" name="foto1"
                               accept="image/*"
                               class="w-full text-sm file:bg-emerald-600 file:text-white file:px-3 file:py-1.5
                                      file:rounded file:border-0 file:mr-4 file:cursor-pointer
                                      hover:file:bg-emerald-500">
                        <p class="text-xs text-gray-500 mt-1">Se comprimirá automáticamente.</p>
                        @error('foto1') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Botones --}}
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('relevamientos.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-200 rounded-lg
                                  hover:bg-gray-300 transition">Cancelar</a>

                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg
                                       hover:bg-emerald-500 transition font-semibold">
                            Guardar
                        </button>
                    </div>
                </form>

            </div>
        </main>
    </div>
</x-app-layout>

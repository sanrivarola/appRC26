<x-app-layout>
    <div class="flex">
        <x-sidebar />

        <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-4 md:p-8">

            <div class="max-w-3xl mx-auto space-y-6">

                <h1 class="text-2xl font-semibold text-purple-700 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"
                         fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0Z" />
                    </svg>
                    Editar relevamiento
                </h1>

                <div class="bg-white shadow rounded-2xl p-6">
                    {{-- errores --}}
                    @if ($errors->any())
                        <div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('relevamientos.update', $relevamiento->id_relevamiento) }}"
                          method="POST"
                          enctype="multipart/form-data"
                          class="space-y-4">
                        @csrf @method('PUT')

                        {{-- título --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Título del relevamiento
                            </label>
                            <input type="text" name="titulorelevamiento"
                                   value="{{ old('titulorelevamiento', $relevamiento->titulorelevamiento) }}"
                                   class="w-full border-gray-300 rounded px-3 py-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        {{-- detalle --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Detalle
                            </label>
                            <textarea name="detallerelevamiento" rows="6"
                                      class="w-full border-gray-300 rounded px-3 py-2 focus:ring-purple-500 focus:border-purple-500">{{ old('detallerelevamiento', $relevamiento->detallerelevamiento) }}</textarea>
                        </div>

                        {{-- foto --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Foto (opcional, máx. 3 MB)
                            </label>

                            @if ($relevamiento->foto1)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/relevamientos/'.$relevamiento->foto1) }}"
                                         alt="Foto actual"
                                         class="h-40 rounded object-cover">
                                </div>
                            @endif

                            <input type="file" name="foto1"
                                   accept="image/*"
                                   class="w-full border-gray-300 rounded px-3 py-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        <div class="pt-4 flex gap-3">
                            <a href="{{ route('relevamientos.index') }}"
                               class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded font-semibold">
                                Cancelar
                            </a>

                            <button type="submit"
                                    class="bg-purple-700 hover:bg-purple-600 text-white px-4 py-2 rounded font-semibold">
                                Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </main>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nueva Área
        </h2>
    </x-slot>

    <div class="max-w-lg mx-auto p-6 bg-white shadow rounded">
        <form action="{{ route('areas.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" id="nombre" name="nombre"
                       class="mt-1 block w-full border-gray-300 rounded"
                       value="{{ old('nombre') }}" required>
                @error('nombre')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
                Guardar
            </button>
        </form>
    </div>
</x-app-layout>

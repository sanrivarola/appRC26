<x-app-layout>
    <div class="max-w-md mx-auto p-6 bg-white shadow rounded">
        <h1 class="text-xl font-semibold mb-4">Editar subárea</h1>

        <form action="{{ route('subareas.update', $subarea) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium">Nombre</label>
                <input type="text" name="nombre" class="mt-1 block w-full border-gray-300 rounded"
                       value="{{ old('nombre', $subarea->nombre) }}" required>
            </div>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Actualizar</button>
        </form>
    </div>
</x-app-layout>

<x-app-layout>
    <div class="max-w-md mx-auto p-6 bg-white shadow rounded">
        <h1 class="text-xl font-semibold mb-4">Nueva subárea</h1>

        <form action="{{ route('subareas.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="areapadre" value="{{ $areaId }}">
            <div>
                <label class="block text-sm font-medium">Nombre</label>
                <input type="text" name="nombre" class="mt-1 block w-full border-gray-300 rounded" required>
            </div>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Guardar</button>
        </form>
    </div>
</x-app-layout>

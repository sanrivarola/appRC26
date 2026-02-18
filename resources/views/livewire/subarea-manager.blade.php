<div class="space-y-6">
    {{-- Mensaje flash --}}
    @if (session('success'))
        <div class="text-green-700 bg-green-100 px-4 py-2 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Formulario --}}
    <div class="flex items-end gap-2">
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700">Nombre de Subárea</label>
            <input type="text" wire:model.defer="nombre"
                   class="mt-1 block w-full border-gray-300 rounded">
            @error('nombre')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        @if($editing)
            <button wire:click="update"
                    class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded">
                Actualizar
            </button>
            <button wire:click="$reset('nombre', 'editing')"
                    class="text-gray-600 px-3 py-2">
                Cancelar
            </button>
        @else
            <button wire:click="create"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Añadir
            </button>
        @endif
    </div>

    {{-- Tabla --}}
    <table class="w-full border border-gray-200 rounded">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left text-sm font-semibold">Subárea</th>
                <th class="px-4 py-2 text-left text-sm font-semibold">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subareas as $sub)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $sub->nombre }}</td>
                    <td class="px-4 py-2 space-x-2">
                        <button wire:click="startEdit({{ $sub->id_subareasinventario }})"
                                class="text-blue-600 hover:underline">
                            Editar
                        </button>
                        <button wire:click="delete({{ $sub->id_subareasinventario }})"
                                class="text-red-600 hover:underline"
                                onclick="return confirm('¿Eliminar esta subárea?')">
                            Eliminar
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

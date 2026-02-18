{{-- resources/views/tareas/extras/create.blade.php --}}
<x-app-layout>
  <div class="flex">
    <x-sidebar />

    <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-4 md:p-8">
      <div class="max-w-xl mx-auto bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">
          Agregar Extra Tarea</span>
        </h2>

        <form action="{{ route('tareas.extras.store', $tarea->id_tarea) }}" method="POST">
          @csrf

          <div class="mb-4">
            <label for="devolucion" class="block text-sm font-medium mb-1">Detalle de la devolución</label>
            <textarea name="devolucion" id="devolucion" rows="4"
                      class="w-full border-gray-300 rounded-md"
                      required>{{ old('devolucion') }}</textarea>
            @error('devolucion')
              <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div class="flex justify-end gap-2">
            <a href="{{ route('tareas.index') }}"
               class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
              Cancelar
            </a>
            <button type="submit"
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
              Guardar Extra
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>
</x-app-layout>

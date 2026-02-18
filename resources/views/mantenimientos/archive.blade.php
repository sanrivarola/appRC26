{{-- resources/views/mantenimientos/archive.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold">Archivar Mantenimiento</h2>
  </x-slot>

  <div class="py-6">
    <div class="max-w-3xl mx-auto bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-medium mb-4">{{ $m->tipo }} — {{ $m->fechacarga }}</h3>
      <form method="POST" action="{{ route('mantenimientos.archive', $m->id_mantenimiento) }}">
        @csrf @method('PATCH')
        <div class="mb-4">
          <label class="block mb-1 font-semibold">Motivo de archivo</label>
          <textarea name="motivo_archivo" rows="4"
                    class="w-full border rounded px-3 py-2">@old('motivo_archivo')</textarea>
          @error('motivo_archivo')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div class="flex justify-end gap-2">
          <a href="{{ route('mantenimientos.index') }}"
             class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancelar</a>
          <button type="submit"
                  class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
            Archivar
          </button>
        </div>
      </form>
    </div>
  </div>
</x-app-layout>

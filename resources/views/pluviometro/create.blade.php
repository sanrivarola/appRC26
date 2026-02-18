{{-- resources/views/pluviometro/create.blade.php  (y edit)--}}
<x-app-layout>
  <div class="flex">
    <x-sidebar />

    <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-6">
      <div class="max-w-lg mx-auto bg-white shadow rounded p-6 space-y-4">

        <h1 class="text-xl font-bold">
          {{ isset($pluviometro) ? 'Editar' : 'Registrar' }} lluvia
        </h1>

        @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            </ul>
        </div>
        @endif


        <form action="{{ isset($pluviometro)
                         ? route('pluviometro.update',$pluviometro)
                         : route('pluviometro.store') }}"
              method="POST">
          @csrf
          @isset($pluviometro) @method('PUT') @endisset

          <label class="block mb-1">Fecha</label>
          <input type="date" name="fechacarga"
                 value="{{ old('fechacarga',$pluviometro->fechacarga ?? now()->toDateString()) }}"
                 class="w-full border rounded px-3 py-2 mb-3" required>

          <label class="block mb-1">Milímetros de agua</label>
          <input type="number" step="0.1" name="medicion"
                 value="{{ old('medicion',$pluviometro->medicion ?? '') }}"
                 class="w-full border rounded px-3 py-2 mb-3" required>

          <label class="block mb-1">Observaciones</label>
          <textarea name="observaciones" rows="3"
                    class="w-full border rounded px-3 py-2 mb-4">{{ old('observaciones',$pluviometro->observaciones ?? '') }}</textarea>

          <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
            Guardar
          </button>
        </form>
      </div>
    </main>
  </div>
</x-app-layout>

{{-- resources/views/relevamientos/show.blade.php --}}
<x-app-layout>
    <div class="flex">
        <x-sidebar />

        <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-4 md:p-8">

            <div class="max-w-3xl mx-auto space-y-6">

                {{-- título + volver --}}
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-semibold text-purple-700 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"
                             fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0Z" />
                        </svg>
                        {{ $relevamiento->titulorelevamiento }}
                    </h1>

                    <a href="{{ route('relevamientos.index') }}"
                       class="text-purple-600 hover:text-purple-800 font-semibold">
                        ← Volver
                    </a>
                </div>

                {{-- tarjeta --}}
                <div class="bg-white shadow rounded-2xl p-6 space-y-4">

                    <p class="text-sm text-gray-500">
                        Cargado el
                        {{ \Carbon\Carbon::parse($relevamiento->fechacarga)->format('d/m/Y') }}
                        por <span class="font-medium">{{ $relevamiento->equipo }}</span>
                    </p>

                    <div class="prose max-w-none">
                        {!! nl2br(e($relevamiento->detallerelevamiento)) !!}
                    </div>

                    @if ($relevamiento->foto1)
                        <div>
                            <img src="{{ asset('storage/relevamientos/'.$relevamiento->foto1) }}"
     alt="Foto relevamiento"
     class="rounded-xl max-h-[480px] object-contain mx-auto">


                        </div>
                    @endif

                    {{-- acciones --}}
                    <div class="flex gap-3 pt-4 border-t">
                        <a href="{{ route('relevamientos.edit', $relevamiento->id_relevamiento) }}"
                           class="inline-flex items-center gap-1 bg-amber-500 hover:bg-amber-600 text-white px-3 py-2 rounded transition">
                            <i class="fa-solid fa-pen-to-square"></i> Editar
                        </a>

                        <form action="{{ route('relevamientos.destroy', $relevamiento->id_relevamiento) }}"
                              method="POST"
                              onsubmit="return confirm('¿Borrar relevamiento?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded transition">
                                <i class="fa-solid fa-trash-can"></i> Borrar
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </main>
    </div>
</x-app-layout>

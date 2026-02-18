{{-- resources/views/proveedores/show.blade.php --}}
<x-app-layout>
    <div class="flex">
        <x-sidebar />

        <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-4 md:p-8">
            <div class="max-w-4xl mx-auto">

                <div class="bg-white shadow rounded-2xl p-8 space-y-6">

                    <h2 class="text-3xl font-semibold text-violet-700 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>
                        Detalle proveedor
                    </h2>

                    <dl class="divide-y divide-gray-200 text-sm">
                        <div class="py-3 grid grid-cols-3 gap-4">
                            <dt class="font-medium text-violet-500">Área</dt>
                            <dd class="col-span-2 text-gray-800">{{ $proveedor->area }}</dd>
                        </div>

                        <div class="py-3 grid grid-cols-3 gap-4">
                            <dt class="font-medium text-violet-500">Nombre</dt>
                            <dd class="col-span-2 text-gray-800">{{ $proveedor->nombre }}</dd>
                        </div>

                        <div class="py-3 grid grid-cols-3 gap-4">
                            <dt class="font-medium text-violet-500">Datos de contacto</dt>
                            <dd class="col-span-2 text-gray-800 whitespace-pre-line">
                                {{ $proveedor->datos }}
                            </dd>
                        </div>

                        <div class="py-3 grid grid-cols-3 gap-4">
                            <dt class="font-medium text-violet-500">Producto / Servicio</dt>
                            <dd class="col-span-2 text-gray-800 whitespace-pre-line">
                                {{ $proveedor->producto }}
                            </dd>
                        </div>

                        <div class="py-3 grid grid-cols-3 gap-4">
                            <dt class="font-medium text-violet-500">Responsable interno</dt>
                            <dd class="col-span-2 text-gray-800">{{ $proveedor->responsable }}</dd>
                        </div>

                        <div class="py-3 grid grid-cols-3 gap-4">
                            <dt class="font-medium text-violet-500">Detalle</dt>
                            <dd class="col-span-2 text-gray-800 whitespace-pre-line">
                                {{ $proveedor->detalle }}
                            </dd>
                        </div>

                        <div class="py-3 grid grid-cols-3 gap-4">
                            <dt class="font-medium text-violet-500">Cargado por</dt>
                            <dd class="col-span-2 text-gray-800">
                                {{ $proveedor->creador }} &nbsp;|&nbsp;
                                {{ \Carbon\Carbon::parse($proveedor->fechacarga)->format('d/m/Y') }}
                            </dd>
                        </div>
                    </dl>

                    {{-- Botones ---------------------------------------------------------}}
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('proveedores.edit', $proveedor) }}"
                           class="inline-flex items-center gap-1 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded transition">
                            <i class="fa-solid fa-pen-to-square"></i> Editar
                        </a>

                        <form action="{{ route('proveedores.destroy', $proveedor) }}"
                              method="POST"
                              onsubmit="return confirm('¿Borrar este proveedor?')" >
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition">
                                <i class="fa-solid fa-trash-can"></i> Borrar
                            </button>
                        </form>

                        <a href="{{ route('proveedores.index') }}"
                           class="ml-auto text-violet-700 hover:text-violet-900 font-semibold">
                            ← Volver al listado
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>

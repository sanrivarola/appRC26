<x-app-layout>
<div class="max-w-lg mx-auto p-6 bg-white shadow rounded space-y-4">
    <h1 class="text-xl font-semibold">Nuevo producto</h1>

    <form action="{{ route('inventario.store') }}" method="POST" x-data="{
        area: '', subareas: @js(
            $areas->flatMap(fn($a)=>$a->subareas->map(fn($s)=>[
                'id'=>$s->id_subareasinventario,
                'name'=>$s->nombre,
                'area'=>$a->id_areainventario
            ]))->values()
        )}">
        @csrf

        <div>
            <label class="block text-sm">Área</label>
                <select x-model="area" name="area_id" class="border rounded w-full">
                    <option value="">Seleccionar…</option>
                    @foreach($areas as $a)
                        <option value="{{ $a->id_areainventario }}">{{ $a->nombre }}</option>
                    @endforeach
                </select>
        </div>

        <div>
            <label class="block text-sm">Subárea</label>
            <select name="subareainventario" class="border rounded w-full">
                <template x-for="opt in subareas.filter(s=>s.area==area)">
                    <option :value="opt.id" x-text="opt.name"></option>
                </template>
            </select>
        </div>

        <div>
            <label class="block text-sm">Producto</label>
            <input name="nombreproducto" class="border rounded w-full" required>
        </div>

        <div>
            <label class="block text-sm">Detalle</label>
            <textarea name="detalle" class="border rounded w-full"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="block text-sm">Vencimiento</label>
                <input type="date" name="vencimiento" class="border rounded w-full">
            </div>
            <div>
                <label class="block text-sm">Fecha carga</label>
                <input type="date" name="fechacarga" value="{{ now()->toDateString() }}" class="border rounded w-full">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="block text-sm">Cantidad</label>
                <input type="number" name="cantidad" value="0" min="0" class="border rounded w-full">
            </div>
            <div>
                <label class="block text-sm">Stock mínimo</label>
                <input type="number" name="stockminimo" value="1" min="0" class="border rounded w-full">
            </div>
        </div>

        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mt-4">
            Guardar
        </button>
    </form>
</div>
</x-app-layout>

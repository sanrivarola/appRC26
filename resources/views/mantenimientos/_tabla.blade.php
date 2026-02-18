{{-- resources/views/mantenimientos/_tabla.blade.php --}}
<div class="overflow-x-auto">
  <table class="min-w-full text-sm">
    <thead class="bg-green-100 text-green-700">
      <tr>
        <th class="p-2 font-bold text-left">Fecha</th>
        <th class="p-2 font-bold text-left">Tipo</th>
        <th class="p-2 font-bold text-left">Responsable</th>
        <th class="p-2 font-bold text-left">Frecuencia</th>
        <th class="p-2 font-bold text-left">Equipo</th>
        <th class="p-2 font-bold text-center">Acciones</th>
      </tr>
    </thead>
    <tbody>
      @forelse($mantenciones as $m)
        <tr class="hover:bg-green-50">
          <td class="p-2">{{ \Carbon\Carbon::parse($m->fechacarga)->format('d/m/Y') }}</td>
          <td class="p-2">
            <a href="{{ route('mantenimientos.show', $m->id_mantenimiento) }}"
               class="font-semibold text-emerald-700 hover:underline">
              {{ $m->tipo }}
            </a>
          </td>
          <td class="p-2">{{ $m->responsable ?: '—' }}</td>
          <td class="p-2">{{ $m->tiempo }}</td>
          <td class="p-2">{{ $m->equipo }}</td>
          <td class="p-2 text-center space-x-2">
            {{-- Editar --}}
            <a href="{{ route('mantenimientos.edit', $m->id_mantenimiento) }}"
               class="text-green-500 hover:text-green-700"
               title="Editar">
              <i class="fa-solid fa-pen-to-square"></i>
            </a>
            {{-- Archivar (si no está) --}}
            @unless($m->archivado)
              <a href="{{ route('mantenimientos.archive.form', $m->id_mantenimiento) }}"
                 class="text-red-500 hover:underline"
                 title="Archivar">
                <i class="fa-solid fa-archive"></i>
              </a>
            @else
              <span class="text-gray-400 text-xs">Archivado</span>
            @endunless
            {{-- Borrar --}}
            <form action="{{ route('mantenimientos.destroy', $m->id_mantenimiento) }}"
                  method="POST" class="inline"
                  onsubmit="return confirm('¿Eliminar este mantenimiento?')">
              @csrf @method('DELETE')
              <button type="submit" class="text-red-600 hover:text-red-800">
                <i class="fa-solid fa-trash-can"></i>
              </button>
            </form>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="p-4 text-center text-gray-500">
            No hay mantenimientos registrados.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- Paginación --}}
<div class="mt-4">
  {{ $mantenciones->appends(request()->query())->links() }}
</div>

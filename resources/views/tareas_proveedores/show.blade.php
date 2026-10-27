<x-app-layout>
<div class="flex">
  <x-sidebar />
  <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-6 md:p-10">
    <div class="max-w-3xl mx-auto space-y-8">

      {{-- FLASH --}}
      @if(session('success'))
        <div class="bg-emerald-100 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-lg">
          {{ session('success') }}
        </div>
      @endif

      {{-- CABECERA --}}
      <div class="flex items-start justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 mb-1 flex-wrap">
            <span class="text-xs px-2 py-1 rounded-full font-medium {{ $tarea->colorTipo() }}">{{ $tarea->tipo }}</span>
            <span class="text-xs px-2 py-1 rounded-full font-medium {{ $tarea->colorEstado() }}">{{ $tarea->estado }}</span>
            <span class="text-xs px-2 py-1 rounded-full font-medium
              {{ $tarea->prioridad === 'Alta' ? 'bg-red-100 text-red-700' : ($tarea->prioridad === 'Media' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
              {{ $tarea->prioridad }}
            </span>
            @if($tarea->resultado)
              <span class="text-xs px-2 py-1 rounded-full font-medium {{ $tarea->colorResultado() }}">{{ $tarea->resultado }}</span>
            @endif
          </div>
          <h1 class="text-2xl font-bold text-slate-800">{{ $tarea->titulo }}</h1>
          <p class="text-sm text-slate-500 mt-1">
            {{ $tarea->proveedor->nombre ?? '—' }} · Creado por {{ $tarea->creador }}
            · {{ optional($tarea->fecha_carga)->format('d/m/Y') }}
          </p>
        </div>
        <div class="flex gap-2 shrink-0">
          <a href="{{ route('tareas-proveedores.edit', $tarea) }}"
             class="px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-semibold transition">
            Editar
          </a>
          <a href="{{ route('tareas-proveedores.index') }}"
             class="px-3 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-sm font-semibold transition">
            Volver
          </a>
        </div>
      </div>

      {{-- DATOS PRINCIPALES --}}
      <div class="bg-white rounded-2xl shadow p-6 space-y-4">
        <div class="grid md:grid-cols-2 gap-4 text-sm">
          <div>
            <div class="text-xs text-slate-400 uppercase tracking-wide mb-1">Proveedor</div>
            <a href="{{ route('proveedores.show', $tarea->proveedor) }}"
               class="font-semibold text-sky-700 hover:underline">
              {{ $tarea->proveedor->nombre ?? '—' }}
            </a>
          </div>
          <div>
            <div class="text-xs text-slate-400 uppercase tracking-wide mb-1">Fecha límite</div>
            @if($tarea->fecha_limite)
              @php $vencida = $tarea->estado !== 'Cerrada' && $tarea->fecha_limite->isPast(); @endphp
              <span class="{{ $vencida ? 'text-red-600 font-bold' : 'text-slate-700 font-semibold' }}">
                {{ $tarea->fecha_limite->format('d/m/Y') }} @if($vencida) ⚠️ Vencida @endif
              </span>
            @else
              <span class="text-slate-400">Sin fecha límite</span>
            @endif
          </div>
          @if($tarea->fecha_cierre)
            <div>
              <div class="text-xs text-slate-400 uppercase tracking-wide mb-1">Fecha cierre</div>
              <span class="font-semibold text-slate-700">{{ $tarea->fecha_cierre->format('d/m/Y') }}</span>
            </div>
          @endif
          @if($tarea->calificacion)
            <div>
              <div class="text-xs text-slate-400 uppercase tracking-wide mb-1">Calificación al proveedor</div>
              <div class="flex items-center gap-1">
                @for($i = 1; $i <= 5; $i++)
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                       class="size-5 {{ $i <= $tarea->calificacion ? 'fill-amber-400 text-amber-400' : 'fill-slate-200 text-slate-200' }}">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/>
                  </svg>
                @endfor
                <span class="text-sm text-slate-500 ml-1">({{ $tarea->calificacion }}/5)</span>
              </div>
            </div>
          @endif
        </div>

        @if($tarea->detalle)
          <div class="border-t pt-4">
            <div class="text-xs text-slate-400 uppercase tracking-wide mb-2">Detalle</div>
            <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50 rounded-lg p-4 border">
              {{ $tarea->detalle }}
            </div>
          </div>
        @endif
      </div>

      {{-- HISTORIAL DE DEVOLUCIONES --}}
      <div class="space-y-4">
        <h2 class="text-lg font-bold text-slate-700 flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-sky-600">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"/>
          </svg>
          Seguimiento ({{ $tarea->devoluciones->count() }})
        </h2>

        @forelse($tarea->devoluciones as $d)
          @php $esCierre = str_starts_with($d->devolucion, '[Cierre]'); @endphp
          <div class="bg-white rounded-xl shadow-sm border {{ $esCierre ? 'border-green-300 bg-green-50' : 'border-slate-200' }} p-4">
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm font-semibold text-slate-700">
                {{ $d->user->name ?? 'Sistema' }}
                @if($esCierre)
                  <span class="ml-2 text-xs bg-green-200 text-green-800 px-2 py-0.5 rounded-full font-medium">Cierre</span>
                @endif
              </span>
              <span class="text-xs text-slate-400">{{ $d->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">
              {{ $esCierre ? substr($d->devolucion, 8) : $d->devolucion }}
            </p>
          </div>
        @empty
          <div class="text-slate-400 text-sm italic py-4">Sin seguimientos registrados aún.</div>
        @endforelse
      </div>

      {{-- ACCIONES: AGREGAR SEGUIMIENTO o CERRAR TAREA --}}
      @if($tarea->estado !== 'Cerrada')

        {{-- Agregar seguimiento --}}
        <div class="bg-white rounded-2xl shadow p-6 space-y-4">
          <h3 class="font-bold text-slate-700">Agregar seguimiento</h3>
          <form method="POST" action="{{ route('tareas-proveedores.devolucion', $tarea) }}">
            @csrf
            <textarea name="devolucion" rows="3" required
                      placeholder="Qué pasó, qué se consultó, respuesta del proveedor…"
                      class="w-full border rounded-lg px-3 py-2 text-sm resize-none focus:ring-sky-400 focus:border-sky-400 mb-3"></textarea>
            <button type="submit"
                    class="px-4 py-2 bg-sky-600 text-white rounded-lg text-sm font-semibold hover:bg-sky-700 transition">
              Guardar seguimiento
            </button>
          </form>
        </div>

        {{-- Botón cerrar tarea --}}
        <div class="bg-white rounded-2xl shadow p-6">
          <h3 class="font-bold text-slate-700 mb-4">Cerrar tarea</h3>
          <button onclick="document.getElementById('modalCerrar').classList.remove('hidden')"
                  class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 transition">
            Marcar como cerrada
          </button>
        </div>

      @else
        {{-- Tarea cerrada --}}
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6 text-center">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10 text-emerald-500 mx-auto mb-2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
          </svg>
          <p class="text-emerald-800 font-semibold">Tarea cerrada el {{ optional($tarea->fecha_cierre)->format('d/m/Y') }}</p>
          @if($tarea->resultado)
            <p class="text-sm text-emerald-700 mt-1">Resultado: <strong>{{ $tarea->resultado }}</strong></p>
          @endif
        </div>
      @endif

    </div>
  </main>
</div>

{{-- MODAL CERRAR TAREA --}}
<div id="modalCerrar" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 {{ $errors->has('resultado') ? '' : 'hidden' }}">
  <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md mx-4">
    <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-emerald-600">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
      </svg>
      Cerrar tarea
    </h3>

    @if($errors->any())
      <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-4">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('tareas-proveedores.cerrar', $tarea) }}" class="space-y-4">
      @csrf

      {{-- Resultado --}}
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Resultado <span class="text-red-500">*</span></label>
        <select name="resultado" required class="w-full border rounded-lg px-3 py-2 text-sm">
          @foreach(['Resuelto','Parcialmente resuelto','Sin respuesta','Pendiente'] as $r)
            <option value="{{ $r }}" @selected(old('resultado') === $r)>{{ $r }}</option>
          @endforeach
        </select>
      </div>

      {{-- Calificación estrellas --}}
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">Calificación al proveedor (opcional)</label>
        <div class="flex items-center gap-1" id="starRating">
          @for($i = 1; $i <= 5; $i++)
            <button type="button" onclick="setStars({{ $i }})" id="star-{{ $i }}"
                    class="text-slate-300 hover:text-amber-400 transition">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-9">
                <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd"/>
              </svg>
            </button>
          @endfor
          <button type="button" onclick="clearStars()" class="ml-2 text-xs text-slate-400 hover:text-red-400 transition">✕ limpiar</button>
        </div>
        <input type="hidden" name="calificacion" id="calificacionInput">
        <p id="starLabel" class="text-xs text-slate-400 mt-1">Sin calificación</p>
      </div>

      {{-- Nota de cierre --}}
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nota de cierre (opcional)</label>
        <textarea name="devolucion" rows="2"
                  placeholder="Resumen de cómo se resolvió, qué dijo el proveedor…"
                  class="w-full border rounded-lg px-3 py-2 text-sm resize-none focus:ring-emerald-400 focus:border-emerald-400">{{ old('devolucion') }}</textarea>
      </div>

      <div class="flex gap-3 justify-end pt-2">
        <button type="button" onclick="document.getElementById('modalCerrar').classList.add('hidden')"
                class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-200 transition">
          Cancelar
        </button>
        <button type="submit"
                class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 transition">
          Cerrar tarea
        </button>
      </div>
    </form>
  </div>
</div>

<script>
const labels = ['','Muy malo','Malo','Regular','Bueno','Excelente'];
function setStars(n) {
  document.getElementById('calificacionInput').value = n;
  document.getElementById('starLabel').textContent = n + '/5 — ' + labels[n];
  for (let i = 1; i <= 5; i++) {
    const btn = document.getElementById('star-' + i);
    btn.classList.toggle('text-amber-400', i <= n);
    btn.classList.toggle('text-slate-300', i > n);
  }
}
function clearStars() {
  document.getElementById('calificacionInput').value = '';
  document.getElementById('starLabel').textContent = 'Sin calificación';
  for (let i = 1; i <= 5; i++) {
    document.getElementById('star-' + i).classList.remove('text-amber-400');
    document.getElementById('star-' + i).classList.add('text-slate-300');
  }
}
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') document.getElementById('modalCerrar').classList.add('hidden');
});
</script>
</x-app-layout>
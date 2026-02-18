@if($vencidos > 0 || $porVencer > 0)
  <div class="bg-white p-4 shadow rounded border mb-6 space-y-4">
    
    {{-- VENCIDOS --}}
    @if($vencidos > 0)
      <div class="flex items-start gap-4 p-4 bg-red-100 border-l-4 border-red-500 rounded">
        <div class="text-red-600 mt-0.5">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
               viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 9v2m0 4h.01M12 5.5c3.038 0 5.5 2.462 5.5 5.5s-2.462 5.5-5.5 5.5-5.5-2.462-5.5-5.5 2.462-5.5 5.5-5.5z" />
          </svg>
        </div>
        <div class="text-red-800">
          <strong class="font-semibold">¡Atención!</strong>
          <p class="text-sm">
            Tienes <span class="font-bold">{{ $vencidos }}</span> mantenimientos vencidos. 
            <a href="{{ route('mantenimientos.index') }}" class="underline text-red-700 hover:text-red-900">
              Ver ahora
            </a>
          </p>
        </div>
      </div>
    @endif

    {{-- POR VENCER --}}
    @if($porVencer > 0)
      <div class="flex items-start gap-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 rounded">
        <div class="text-yellow-600 mt-0.5">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
               viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 9v2m0 4h.01M12 5.5c3.038 0 5.5 2.462 5.5 5.5s-2.462 5.5-5.5 5.5-5.5-2.462-5.5-5.5 2.462-5.5 5.5-5.5z" />
          </svg>
        </div>
        <div class="text-yellow-800">
          <strong class="font-semibold">Aviso</strong>
          <p class="text-sm">
            Tienes <span class="font-bold">{{ $porVencer }}</span> mantenimientos por vencer en los próximos 7 días. 
            <a href="{{ route('mantenimientos.index') }}" class="underline text-yellow-700 hover:text-yellow-900">
              Ver ahora
            </a>
          </p>
        </div>
      </div>
    @endif

  </div>
@endif

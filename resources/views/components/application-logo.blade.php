<div class="mb-6">
     @role('admin')

        <a href="{{ url('/admin') }}">
            <img src="{{ asset('img/bg-raracuenca.png') }}"
                 alt="Rara Cuenca App" class="h-24 w-auto">
        </a>
    @elserole('editor')
        <a href="{{ url('/editor') }}">
            <div class="block h-6 w-auto fill-current text-white">
    <img src="{{ asset('img/bg-raracuenca.png') }}"
                 alt="Rara Cuenca App" class="h-24 w-auto">
            </div>
        </a>
     @elserole('colaborador')
        <a href="{{ url('/colaborador') }}">
            <img src="{{ asset('img/bg-raracuenca.png') }}"
                 alt="Rara Cuenca App" class="h-24 w-auto">
        </a>
    @else
        <a href="{{ url('/') }}">
            <img src="{{ asset('img/bg-raracuenca.png') }}"
                 alt="Rara Cuenca App" class="h-48 w-auto">
        </a>
      @endrole
    </div>
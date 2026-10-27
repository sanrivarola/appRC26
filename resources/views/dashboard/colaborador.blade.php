<x-app-layout>
    <div>
        <x-sidebar />
        <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-4 md:p-8 transition-all duration-300">
                        <!---->
<div
    x-data="{ visible: localStorage.getItem('aviso_dashboard') !== 'cerrado' }"
    x-show="visible"
    class="bg-amber-100 border-l-4 border-amber-500 text-amber-800 p-4 rounded-lg mb-4"
>
    <div class="flex justify-between items-start">

        <button
            @click="
                visible = false;
                localStorage.setItem('aviso_dashboard', 'cerrado');
            "
            class="text-amber-700 hover:text-amber-900 text-xl leading-none"
        >
            ×
        </button>
    </div>
</div>
            <!---->

            <div class="max-w-6xl mx-auto mt-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
               {{-- card horarios --}}
                <a href="{{ route('horario.card') }}" class="group bg-white shadow rounded-2xl p-6 flex items-center gap-4 hover:shadow-lg hover:-translate-y-1 transition-all border border-transparent hover:border-orange-300">
                    <div class="flex-shrink-0 bg-orange-50 rounded-full p-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-orange-500 ">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>

                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-orange-500 group-hover:text-orange-700">Horarios</h3>
                        <p class="text-sm text-gray-500">Carga de horarios del equipo</p>
                    </div>
                    <span class="ml-auto">
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-orange-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </a>


                {{-- Residencia --}}
                <a href="{{ route('residencia.card') }}" class="group bg-white shadow rounded-2xl p-6 flex items-center gap-4 hover:shadow-lg hover:-translate-y-1 transition-all border border-transparent hover:border-indigo-300">
                    <div class="flex-shrink-0 bg-indigo-50 rounded-full p-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-indigo-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-indigo-500 group-hover:text-indigo-700">Residencia</h3>
                        <p class="text-sm text-gray-500">Registro de ingreso y egreso de residencia</p>
                    </div>
                    <span class="ml-auto">
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-indigo-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </a>

                {{-- Tareas --}}
                <a href="{{ route('tareas.index') }}"
                   class="group bg-white shadow rounded-2xl p-6 flex items-center gap-4 flex-nowrap
                          hover:shadow-lg hover:-translate-y-1 transition-all
                          border border-transparent hover:border-yellow-500">
                    <div class="flex-shrink-0 bg-yellow-50 rounded-full p-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-yellow-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" />
                            </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-yellow-500 group-hover:text-yellow-600">Tareas</h3>
                        <p class="text-sm text-gray-500">Gestión y seguimiento de tareas diarias</p>
                    </div>
                    <span class="ml-auto">
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-yellow-600 transition" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </a>


                {{-- Mantenimiento --}}
                <a href="{{ route('mantenimientos.index') }}"
                   class="group bg-white shadow rounded-2xl p-6 flex items-center gap-4 flex-nowrap
                          hover:shadow-lg hover:-translate-y-1 transition-all
                          border border-transparent hover:border-green-500">
                    <div class="flex-shrink-0 bg-green-50 rounded-full p-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-green-500 group-hover:text-green-600">Mantenimiento</h3>
                        <p class="text-sm text-gray-500">Registro de mantenimientos herramientas, cuatriciclos, etc</p>
                    </div>
                    <span class="ml-auto">
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-green-600 transition" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </a>

                {{-- Relevamientos --}}
                <a href="{{ route('relevamientos.index') }}"
                   class="group bg-white shadow rounded-2xl p-6 flex items-center gap-4 flex-nowrap
                          hover:shadow-lg hover:-translate-y-1 transition-all
                          border border-transparent hover:border-emerald-300">
                    <div class="flex-shrink-0 bg-emerald-50 rounded-full p-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-emerald-700" fill="none"
                             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3 7h2l2-3h10l2 3h2a2 2 0 012 2v9a2 2 0 01-2 2H3a2 2 0 01-2-2V9a2 2 0 012-2z"/>
                            <circle cx="12" cy="13" r="4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-emerald-700 group-hover:text-emerald-900">
                            Relevamientos
                        </h3>
                        <p class="text-sm text-gray-500">
                            Crea informes con fotos y detalles de campo
                        </p>
                    </div>
                    <span class="ml-auto">
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-emerald-600 transition" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </a>



                {{-- Pluviometro --}}
                <a href="{{ route('pluviometro.index') }}"
                   class="group bg-white shadow rounded-2xl p-6 flex items-center gap-4 flex-nowrap
                          hover:shadow-lg hover:-translate-y-1 transition-all
                          border border-transparent hover:border-sky-500">
                    <div class="flex-shrink-0 bg-sky-50 rounded-full p-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-sky-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 0 0 4.5 4.5H18a3.75 3.75 0 0 0 1.332-7.257 3 3 0 0 0-3.758-3.848 5.25 5.25 0 0 0-10.233 2.33A4.502 4.502 0 0 0 2.25 15Z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-sky-500 group-hover:text-sky-600">Pluviómetro</h3>
                        <p class="text-sm text-gray-500">Registro de los milímetros de lluvia recolectados en cada jornada de precipitación.</p>
                    </div>
                    <span class="ml-auto">
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-sky-600 transition" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </a>

                {{-- Compras --}}
                <a href="{{ route('compras.index') }}"
                   class="group bg-white shadow rounded-2xl p-6 flex items-center gap-4 flex-nowrap
                          hover:shadow-lg hover:-translate-y-1 transition-all
                          border border-transparent hover:border-fuchsia-300">
                    <div class="flex-shrink-0 bg-fuchsia-50 rounded-full p-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-fuchsia-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-fuchsia-500 group-hover:text-fuchsia-600">
                            Compras
                        </h3>
                        <p class="text-sm text-gray-500">
                            Pedidos realizados por el equipo para comprar
                        </p>
                    </div>
                    <span class="ml-auto">
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-fuchsia-600 transition" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </a>

                {{-- Inventario --}}
                <a href="{{ route('inventario.index') }}"
                   class="group bg-white shadow rounded-2xl p-6 flex items-center gap-4 flex-nowrap
                          hover:shadow-lg hover:-translate-y-1 transition-all
                          border border-transparent hover:border-amber-300">
                    <div class="flex-shrink-0 bg-amber-50 rounded-full p-4">
                        <svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5 text-amber-600' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z' />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-amber-600 group-hover:text-amber-900">
                            Inventario
                        </h3>
                        <p class="text-sm text-gray-500">
                            Administrar inventario
                        </p>
                    </div>
                    <span class="ml-auto">
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-amber-600 transition" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </a>




            </div>

            

        </main>
    </div>
</x-app-layout>

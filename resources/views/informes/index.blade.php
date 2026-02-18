<x-app-layout>
    <div class="flex">
        <x-sidebar />

        <main class="flex-1 bg-slate-100 dark:bg-slate-900 min-h-screen md:ml-64 p-6 md:p-10">
            <div class="max-w-6xl mx-auto backdrop-blur-md bg-white/80 dark:bg-slate-800/60 shadow-xl
                        rounded-3xl p-8">

                <h1 class="text-3xl font-extrabold text-indigo-700 dark:text-indigo-400 mb-8 flex items-center gap-3">
                    <i class="fa-solid fa-chart-column"></i>
                    Informes de tareas
                </h1>

                {{-- Componente gráfico --}}
                <livewire:informes.tareas-mes />
                <button wire:click="exportExcel"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    <i class="fa-solid fa-file-excel"></i> Exportar Excel
                </button>
            </div>
        </main>
    </div>
</x-app-layout>

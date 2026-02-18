{{-- resources/views/livewire/informes/tareas-mes.blade.php --}}
<div x-data="chartComponent()"
     x-init="init()"
     class="space-y-6">

    {{-- Controles --}}
    <div class="flex flex-wrap gap-4 items-end">
        <select wire:model="preset" class="border rounded p-2">
            <option value="7d">Últimos 7 días</option>
            <option value="30d">Últimos 30 días</option>
            <option value="365d">Último año</option>
            <option value="custom">Rango personalizado</option>
        </select>

        @if ($preset === 'custom')
            <div class="flex gap-2">
                <input type="date" wire:model="from" class="border rounded p-2">
                <input type="date" wire:model="to"   class="border rounded p-2">
            </div>
        @endif
    </div>

    {{-- Gráfico (ignorado por Livewire) --}}
    <canvas x-ref="canvas" wire:ignore height="140"></canvas>
<p class="text-xs text-gray-500">
    Desde: <span x-text="data.range_from"></span>
    &nbsp;–&nbsp;
    Hasta: <span x-text="data.range_to"></span>
</p>
    <script>
        function chartComponent () {
            /* fuera del scope reactivo  →  NO se serializa */
            let chartInstance = null

            return {
                data: @entangle('chartPayload').live,

                init () {
                    /* primer render y redibujado cada vez que cambian los datos */
                    this.$watch('data', () => this.renderOrUpdate())
                    this.renderOrUpdate()
                },

                renderOrUpdate () {
                    this.$nextTick(() => {
                        if (!this.$refs.canvas) return
                        if (!this.data.labels.length) return

                        if (!chartInstance) {
                            const ctx = this.$refs.canvas.getContext('2d')
                            chartInstance = new Chart(ctx, this.config())
                        } else {
                            chartInstance.data.labels           = this.data.labels
                            chartInstance.data.datasets[0].data = this.data.values
                            chartInstance.update()
                        }
                    })
                },

                config () {
                    return {
                        type: 'bar',
                        data: {
                            labels: this.data.labels,
                            datasets: [{
                                label: 'Tareas finalizadas',
                                data:  this.data.values,
                                backgroundColor: '#34d399',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            plugins: { legend: { display: false } },
                            scales:  { y: { beginAtZero: true, ticks: { precision: 0 } } }
                        }
                    }
                }
            }
        }
    </script>
</div>

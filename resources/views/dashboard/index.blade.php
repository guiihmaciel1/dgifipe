<x-layouts.app title="Dashboard">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-apple-text tracking-tight">Olá, {{ $user->name }}</h1>
        <p class="text-apple-muted mt-1">Veja o resumo do seu dia.</p>
    </div>

    @if($stats['newOpportunities'] > 0)
        <a href="{{ route('alerts.index') }}" class="block mb-6">
            <div class="relative overflow-hidden rounded-apple-xl bg-gradient-to-r from-apple-green/10 to-apple-green/5 p-4 ring-1 ring-apple-green/20">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-apple-green/20 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-apple-green">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-apple-text">
                            {{ $stats['newOpportunities'] }} {{ $stats['newOpportunities'] === 1 ? 'nova oportunidade' : 'novas oportunidades' }}
                        </p>
                        <p class="text-sm text-apple-muted">Anúncios abaixo do preço de mercado</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-apple-muted flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </div>
            </div>
        </a>
    @endif

    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4 lg:gap-4 mb-6">
        <x-card class="!p-4 lg:!p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-apple bg-apple-blue/10 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-apple-blue">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <p class="text-xs font-medium text-apple-muted uppercase tracking-wider">Hoje</p>
            </div>
            <p class="text-2xl lg:text-3xl font-bold text-apple-text">{{ $stats['todayEvaluations'] }}</p>
            <p class="text-xs text-apple-muted mt-1">avaliações</p>
        </x-card>

        <x-card class="!p-4 lg:!p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-apple bg-apple-green/10 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-apple-green">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                    </svg>
                </div>
                <p class="text-xs font-medium text-apple-muted uppercase tracking-wider">Total</p>
            </div>
            <p class="text-2xl lg:text-3xl font-bold text-apple-text">{{ $stats['totalEvaluations'] }}</p>
            <p class="text-xs text-apple-muted mt-1">avaliações</p>
        </x-card>

        <x-card class="!p-4 lg:!p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-apple bg-apple-orange/10 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-apple-orange">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                    </svg>
                </div>
                <p class="text-xs font-medium text-apple-muted uppercase tracking-wider">Anúncios</p>
            </div>
            <p class="text-2xl lg:text-3xl font-bold text-apple-text">{{ $stats['totalListings'] }}</p>
            <p class="text-xs text-apple-muted mt-1">últimos 7 dias</p>
        </x-card>

        <x-card class="!p-4 lg:!p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-apple bg-apple-red/10 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-apple-red">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                    </svg>
                </div>
                <p class="text-xs font-medium text-apple-muted uppercase tracking-wider">Oportunidades</p>
            </div>
            <p class="text-2xl lg:text-3xl font-bold text-apple-text">{{ $stats['newOpportunities'] }}</p>
            <p class="text-xs text-apple-muted mt-1">pendentes</p>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6 mb-6">
        <x-card class="lg:col-span-2 !p-4 lg:!p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-apple-text">Avaliações por Dia</h2>
                <span class="text-xs text-apple-muted">Últimos 7 dias</span>
            </div>
            <div class="h-48 lg:h-56" x-data="weeklyChart()" x-init="init()">
                <canvas x-ref="chart" class="w-full h-full"></canvas>
            </div>
        </x-card>

        <x-card class="!p-4 lg:!p-6">
            <h2 class="text-base font-semibold text-apple-text mb-4">Modelos Populares</h2>
            @if(empty($topModels))
                <p class="text-sm text-apple-muted py-4 text-center">Sem dados ainda.</p>
            @else
                <div class="space-y-3">
                    @php $maxCount = max($topModels) ?: 1; @endphp
                    @foreach($topModels as $model => $count)
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <p class="text-sm font-medium text-apple-text truncate pr-2">{{ $model }}</p>
                                <span class="text-xs text-apple-muted flex-shrink-0">{{ $count }}</span>
                            </div>
                            <div class="w-full h-1.5 bg-apple-bg rounded-full overflow-hidden">
                                <div class="h-full bg-apple-blue rounded-full transition-all duration-500"
                                     style="width: {{ round(($count / $maxCount) * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>
    </div>

    <div class="grid grid-cols-3 gap-3 mb-6 lg:hidden">
        <a href="{{ route('evaluator') }}" class="flex flex-col items-center gap-2 p-4 bg-white rounded-apple-xl shadow-apple active:scale-[0.97] transition-transform">
            <div class="w-10 h-10 rounded-full bg-apple-blue/10 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-apple-blue">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V13.5Zm0 2.25h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V18Zm2.498-6.75h.007v.008h-.007v-.008Zm0 2.25h.007v.008h-.007V13.5Zm0 2.25h.007v.008h-.007v-.008Zm0 2.25h.007v.008h-.007V18Zm2.504-6.75h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V13.5Zm0 2.25h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V18Zm2.498-6.75h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V13.5ZM8.25 6h7.5v2.25h-7.5V6ZM12 2.25c-1.892 0-3.758.11-5.593.322C5.307 2.7 4.5 3.65 4.5 4.757V19.5a2.25 2.25 0 0 0 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25V4.757c0-1.108-.806-2.057-1.907-2.185A48.507 48.507 0 0 0 12 2.25Z" />
                </svg>
            </div>
            <span class="text-xs font-medium text-apple-text">Avaliar</span>
        </a>
        <a href="{{ route('alerts.index') }}" class="flex flex-col items-center gap-2 p-4 bg-white rounded-apple-xl shadow-apple active:scale-[0.97] transition-transform">
            <div class="w-10 h-10 rounded-full bg-apple-green/10 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-apple-green">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <span class="text-xs font-medium text-apple-text">Oportunidades</span>
        </a>
        <a href="{{ route('market-radar') }}" class="flex flex-col items-center gap-2 p-4 bg-white rounded-apple-xl shadow-apple active:scale-[0.97] transition-transform">
            <div class="w-10 h-10 rounded-full bg-apple-orange/10 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-apple-orange">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M3 20.25h18M3.75 3v.75m17.25-.75v.75M3.75 20.25v.75m17.25-.75v.75" />
                </svg>
            </div>
            <span class="text-xs font-medium text-apple-text">Radar</span>
        </a>
    </div>

    <div class="hidden lg:grid lg:grid-cols-3 gap-4 mb-6">
        <a href="{{ route('evaluator') }}" class="group flex items-center gap-4 p-5 bg-white rounded-apple-xl shadow-apple hover:shadow-apple-md transition-all duration-200">
            <div class="w-11 h-11 rounded-apple bg-apple-blue/10 flex items-center justify-center flex-shrink-0 group-hover:bg-apple-blue/20 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-apple-blue">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V13.5Zm0 2.25h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V18Zm2.498-6.75h.007v.008h-.007v-.008Zm0 2.25h.007v.008h-.007V13.5Zm0 2.25h.007v.008h-.007v-.008Zm0 2.25h.007v.008h-.007V18Zm2.504-6.75h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V13.5Zm0 2.25h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V18Zm2.498-6.75h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V13.5ZM8.25 6h7.5v2.25h-7.5V6ZM12 2.25c-1.892 0-3.758.11-5.593.322C5.307 2.7 4.5 3.65 4.5 4.757V19.5a2.25 2.25 0 0 0 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25V4.757c0-1.108-.806-2.057-1.907-2.185A48.507 48.507 0 0 0 12 2.25Z" />
                </svg>
            </div>
            <div>
                <p class="font-semibold text-apple-text text-sm">Nova Avaliação</p>
                <p class="text-xs text-apple-muted">Avaliar um dispositivo</p>
            </div>
        </a>
        <a href="{{ route('alerts.index') }}" class="group flex items-center gap-4 p-5 bg-white rounded-apple-xl shadow-apple hover:shadow-apple-md transition-all duration-200">
            <div class="w-11 h-11 rounded-apple bg-apple-green/10 flex items-center justify-center flex-shrink-0 group-hover:bg-apple-green/20 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-apple-green">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <div>
                <p class="font-semibold text-apple-text text-sm">Oportunidades</p>
                <p class="text-xs text-apple-muted">Ver anúncios abaixo do mercado</p>
            </div>
        </a>
        <a href="{{ route('market-radar') }}" class="group flex items-center gap-4 p-5 bg-white rounded-apple-xl shadow-apple hover:shadow-apple-md transition-all duration-200">
            <div class="w-11 h-11 rounded-apple bg-apple-orange/10 flex items-center justify-center flex-shrink-0 group-hover:bg-apple-orange/20 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-apple-orange">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M3 20.25h18M3.75 3v.75m17.25-.75v.75M3.75 20.25v.75m17.25-.75v.75" />
                </svg>
            </div>
            <div>
                <p class="font-semibold text-apple-text text-sm">Market Radar</p>
                <p class="text-xs text-apple-muted">Explorar anúncios do mercado</p>
            </div>
        </a>
    </div>

    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-base font-semibold text-apple-text">Últimas Avaliações</h2>
        <a href="{{ route('history') }}" class="text-sm text-apple-blue hover:underline">Ver todas</a>
    </div>

    @if($recentSimulations->isEmpty())
        <x-card>
            <div class="text-center py-10">
                <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-apple-bg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-7 h-7 text-apple-muted/60">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                    </svg>
                </div>
                <p class="font-medium text-apple-text mb-1">Nenhuma avaliação ainda</p>
                <p class="text-sm text-apple-muted mb-5">Comece avaliando seu primeiro dispositivo.</p>
                <a href="{{ route('evaluator') }}" class="btn-primary text-sm !px-5 !py-2.5">
                    Avaliar agora
                </a>
            </div>
        </x-card>
    @else
        <div class="space-y-3">
            @foreach($recentSimulations as $sim)
                <a href="{{ route('history.show', $sim->evaluationSession) }}" class="block">
                    <x-card class="hover:shadow-apple-md active:scale-[0.99] transition-all duration-200">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-semibold text-apple-text truncate">{{ $sim->model }} {{ $sim->storage }}</p>
                                <p class="text-sm text-apple-muted mt-0.5">
                                    Bateria: {{ $sim->battery_health }}% · {{ $sim->listings_count }} anúncios
                                </p>
                                <p class="text-xs text-apple-muted mt-1">
                                    {{ $sim->evaluationSession->user->name }} · {{ $sim->created_at->format('d/m H:i') }}
                                </p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <x-price-display :value="$sim->suggested_price" size="small" color="green" />
                                <p class="text-xs text-apple-muted mt-0.5">sugerido</p>
                            </div>
                        </div>
                    </x-card>
                </a>
            @endforeach
        </div>
    @endif

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
        function weeklyChart() {
            return {
                chart: null,
                init() {
                    if (this.chart) {
                        this.chart.destroy();
                    }

                    const existing = Chart.getChart(this.$refs.chart);
                    if (existing) {
                        existing.destroy();
                    }

                    const ctx = this.$refs.chart.getContext('2d');
                    const labels = @json($chart['labels']);
                    const data = @json($chart['data']);
                    const maxVal = Math.max(...data, 1);

                    this.chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [{
                                data,
                                backgroundColor: 'rgba(0, 113, 227, 0.15)',
                                hoverBackgroundColor: 'rgba(0, 113, 227, 0.3)',
                                borderColor: 'rgba(0, 113, 227, 0.8)',
                                borderWidth: 1,
                                borderRadius: 8,
                                borderSkipped: false,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#1D1D1F',
                                    titleFont: { size: 12, weight: '500' },
                                    bodyFont: { size: 13, weight: '600' },
                                    padding: 10,
                                    cornerRadius: 10,
                                    displayColors: false,
                                    callbacks: {
                                        label: ctx => ctx.raw + (ctx.raw === 1 ? ' avaliação' : ' avaliações')
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    suggestedMax: maxVal + 1,
                                    ticks: {
                                        stepSize: 1,
                                        color: '#6E6E73',
                                        font: { size: 11 }
                                    },
                                    grid: {
                                        color: 'rgba(210, 210, 215, 0.4)',
                                        drawBorder: false,
                                    },
                                    border: { display: false }
                                },
                                x: {
                                    ticks: {
                                        color: '#6E6E73',
                                        font: { size: 11 }
                                    },
                                    grid: { display: false },
                                    border: { display: false }
                                }
                            }
                        }
                    });
                }
            }
        }
    </script>
    @endpush
</x-layouts.app>

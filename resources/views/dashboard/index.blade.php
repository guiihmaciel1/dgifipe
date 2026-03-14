<x-layouts.app title="Dashboard">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-apple-text tracking-tight">Dashboard</h1>
        <p class="text-apple-muted mt-1">Bem-vindo, {{ auth()->user()->name }}.</p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-card>
            <p class="label">Avaliações Hoje</p>
            <p class="text-3xl font-bold text-apple-text">{{ $todayEvaluations }}</p>
        </x-card>

        <x-card>
            <p class="label">Total Avaliações</p>
            <p class="text-3xl font-bold text-apple-text">{{ $totalEvaluations }}</p>
        </x-card>

        <x-card>
            <p class="label">Anúncios Ativos</p>
            <p class="text-3xl font-bold text-apple-text">{{ $totalListings }}</p>
        </x-card>

        <x-card>
            <p class="label">Sua Função</p>
            <p class="text-lg font-semibold text-apple-blue capitalize">{{ auth()->user()->role }}</p>
        </x-card>
    </div>

    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-apple-text">Últimas Avaliações</h2>
        <a href="{{ route('history') }}" class="text-sm text-apple-blue hover:underline">Ver todas</a>
    </div>

    @if($recentSimulations->isEmpty())
        <x-card>
            <p class="text-center text-apple-muted py-8">Nenhuma avaliação realizada ainda.</p>
        </x-card>
    @else
        <div class="space-y-3">
            @foreach($recentSimulations as $sim)
                <x-card>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-apple-text">{{ $sim->model }} {{ $sim->storage }}</p>
                            <p class="text-sm text-apple-muted">Bateria: {{ $sim->battery_health }}% · {{ $sim->listings_count }} anúncios</p>
                        </div>
                        <div class="text-right">
                            <x-price-display :value="$sim->suggested_price" size="small" color="green" />
                            <p class="text-xs text-apple-muted mt-0.5">Preço sugerido</p>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif
</x-layouts.app>

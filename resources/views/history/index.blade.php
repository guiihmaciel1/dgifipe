<x-layouts.app title="Histórico">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-apple-text tracking-tight">Histórico de Avaliações</h1>
        <p class="text-apple-muted mt-1">Todas as avaliações realizadas pela sua equipe.</p>
    </div>

    @if($sessions->isEmpty())
        <x-card>
            <p class="text-center text-apple-muted py-12">Nenhuma avaliação realizada ainda.</p>
        </x-card>
    @else
        <div class="space-y-3">
            @foreach($sessions as $session)
                @foreach($session->simulations as $sim)
                    <a href="{{ route('history.show', $session) }}" class="block">
                        <x-card class="hover:shadow-apple-md transition-shadow duration-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-apple-text">{{ $sim->model }} {{ $sim->storage }}</p>
                                    <p class="text-sm text-apple-muted">
                                        {{ $session->user->name }} · Bateria: {{ $sim->battery_health }}%
                                        · {{ $sim->listings_count }} anúncios
                                    </p>
                                    <p class="text-xs text-apple-muted mt-1">{{ $session->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="text-right">
                                    <x-price-display :value="$sim->suggested_price" size="small" color="green" />
                                    <p class="text-xs text-apple-muted mt-0.5">sugerido</p>
                                </div>
                            </div>
                        </x-card>
                    </a>
                @endforeach
            @endforeach
        </div>

        <div class="mt-6">
            {{ $sessions->links() }}
        </div>
    @endif
</x-layouts.app>

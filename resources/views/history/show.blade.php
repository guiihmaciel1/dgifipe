<x-layouts.app title="Detalhes da Avaliação">
    <div class="mb-6">
        <a href="{{ route('history') }}" class="text-sm text-apple-blue hover:underline">&larr; Voltar ao histórico</a>
        <h1 class="text-2xl font-bold text-apple-text tracking-tight mt-2">Detalhes da Avaliação</h1>
        <p class="text-apple-muted mt-1">
            Realizada por {{ $session->user->name }} em {{ $session->created_at->format('d/m/Y \à\s H:i') }}
        </p>
    </div>

    @foreach($session->simulations as $sim)
        <div class="space-y-4">
            <x-card>
                <p class="label">Modelo Avaliado</p>
                <p class="text-xl font-semibold text-apple-text">{{ $sim->model }} — {{ $sim->storage }}</p>
                <p class="text-sm text-apple-muted mt-1">Bateria: {{ $sim->battery_health }}%</p>
                @if(!empty($sim->conditions))
                    <div class="flex flex-wrap gap-2 mt-3">
                        @php
                            $labels = [
                                'no_box' => 'Sem caixa',
                                'no_cable' => 'Sem cabo',
                                'screen_replaced' => 'Tela trocada',
                                'face_id_issue' => 'Face ID com problema',
                            ];
                        @endphp
                        @foreach($sim->conditions as $cond)
                            <span class="badge-warning">{{ $labels[$cond] ?? $cond }}</span>
                        @endforeach
                    </div>
                @endif
            </x-card>

            <x-card>
                <p class="label">Preço Sugerido de Compra</p>
                <x-price-display :value="$sim->suggested_price" size="large" color="green" />
            </x-card>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-card>
                    <p class="label">Média do Mercado</p>
                    <x-price-display :value="$sim->market_average" size="medium" />
                </x-card>
                <x-card>
                    <p class="label">Menor Preço</p>
                    <x-price-display :value="$sim->price_min" size="medium" />
                </x-card>
                <x-card>
                    <p class="label">Maior Preço</p>
                    <x-price-display :value="$sim->price_max" size="medium" />
                </x-card>
            </div>

            <x-card>
                <p class="label">Anúncios Analisados</p>
                <p class="text-2xl font-bold text-apple-text">{{ $sim->listings_count }}</p>
                @if($sim->low_data_warning)
                    <p class="text-sm text-apple-orange mt-2">⚠ Poucos anúncios disponíveis.</p>
                @endif
            </x-card>
        </div>
    @endforeach
</x-layouts.app>

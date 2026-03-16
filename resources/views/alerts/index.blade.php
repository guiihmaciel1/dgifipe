<x-layouts.app title="Oportunidades">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-apple-text tracking-tight">Oportunidades de Compra</h1>
        <p class="text-apple-muted mt-1">Anúncios com preço abaixo do valor de mercado.</p>
    </div>

    @if($alerts->isEmpty())
        <x-card>
            <div class="text-center py-16">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-16 h-16 mx-auto text-apple-muted/40 mb-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>
                <p class="text-apple-muted font-medium">Nenhuma oportunidade no momento</p>
                <p class="text-apple-muted text-sm mt-1">Novas oportunidades aparecem quando o robô encontra anúncios abaixo do preço de mercado.</p>
            </div>
        </x-card>
    @else
        <div class="space-y-3">
            @foreach($alerts as $alert)
                <x-card class="{{ $alert->status === 'new' ? 'ring-1 ring-apple-green/40 bg-apple-green/5' : '' }}">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <p class="font-semibold text-apple-text">{{ $alert->model }} {{ $alert->storage }}</p>
                                @if($alert->status === 'new')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold uppercase bg-apple-green/20 text-apple-green">Novo</span>
                                @endif
                            </div>

                            <p class="text-sm text-apple-muted truncate">{{ $alert->title ?: 'Sem título' }}</p>

                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-sm">
                                <span class="text-apple-text">
                                    Anúncio: <strong class="text-apple-green">R$ {{ number_format($alert->listing_price, 2, ',', '.') }}</strong>
                                </span>
                                <span class="text-apple-muted">
                                    Mercado: R$ {{ number_format($alert->market_average, 2, ',', '.') }}
                                </span>
                                <span class="text-apple-muted">
                                    Compra: R$ {{ number_format($alert->suggested_buy_price, 2, ',', '.') }}
                                </span>
                            </div>

                            <div class="flex items-center gap-3 mt-2 text-xs text-apple-muted">
                                <span>{{ $alert->city }}</span>
                                <span class="badge-success">{{ ucfirst($alert->source) }}</span>
                                <span>{{ $alert->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>

                        <div class="flex flex-col items-end gap-2 flex-shrink-0">
                            <div class="text-right">
                                <p class="text-lg font-bold text-apple-green">+{{ number_format($alert->profit_percentage, 0) }}%</p>
                                <p class="text-xs text-apple-muted">R$ {{ number_format($alert->potential_profit, 0, ',', '.') }} lucro</p>
                            </div>

                            <div class="flex items-center gap-1.5">
                                @if($alert->url)
                                    <a href="{{ $alert->url }}" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-apple bg-apple-blue text-white hover:bg-apple-blue/90 transition-colors">
                                        Ver anúncio
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                        </svg>
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('alerts.dismiss', $alert) }}">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="px-2.5 py-1 text-xs font-medium rounded-apple text-apple-muted hover:text-apple-red hover:bg-apple-red/10 transition-colors" title="Dispensar">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $alerts->links() }}
        </div>
    @endif
</x-layouts.app>

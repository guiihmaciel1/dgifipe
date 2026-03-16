<x-layouts.app title="Market Radar">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-apple-text tracking-tight">Market Radar</h1>
        <p class="text-apple-muted mt-1">Anúncios coletados nos últimos 7 dias.</p>
    </div>

    <x-card class="mb-6" x-data="marketRadarFilter()">
        <form method="GET" action="{{ route('market-radar') }}" class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <select name="model" class="input-field text-sm" x-model="selectedModel" @change="onModelChange()">
                <option value="">Todos os modelos</option>
                @foreach(array_keys($models) as $model)
                    <option value="{{ $model }}">{{ $model }}</option>
                @endforeach
            </select>

            <select name="storage" class="input-field text-sm" x-ref="storageSelect">
                <option value="">Todas capacidades</option>
                @foreach(['64GB','128GB','256GB','512GB','1TB'] as $s)
                    <option value="{{ $s }}"
                            :disabled="!storageVisible('{{ $s }}')"
                            :style="storageVisible('{{ $s }}') ? '' : 'display:none'"
                            @if(request('storage') === $s) selected @endif
                    >{{ $s }}</option>
                @endforeach
            </select>

            <select name="source" class="input-field text-sm">
                <option value="">Todas fontes</option>
                <option value="facebook" @selected(request('source') === 'facebook')>Facebook</option>
                <option value="olx" @selected(request('source') === 'olx')>OLX</option>
                <option value="manual" @selected(request('source') === 'manual')>Manual</option>
            </select>

            <button type="submit" class="btn-primary text-sm">Filtrar</button>
        </form>
    </x-card>

    @if($listings->isEmpty())
        <x-card>
            <p class="text-center text-apple-muted py-12">Nenhum anúncio encontrado.</p>
        </x-card>
    @else
        <div class="space-y-3">
            @foreach($listings as $listing)
                <a href="{{ $listing->url ?: '#' }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="block transition-transform active:scale-[0.98] {{ $listing->url ? '' : 'pointer-events-none' }}">
                    <x-card class="hover:ring-1 hover:ring-apple-blue/30">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-apple-text truncate">{{ $listing->model }} {{ $listing->storage }}</p>
                                <p class="text-sm text-apple-muted truncate">{{ $listing->title ?? 'Sem título' }}</p>
                                <div class="flex items-center gap-3 mt-1 text-xs text-apple-muted">
                                    <span>{{ $listing->city }}</span>
                                    <span class="badge-{{ $listing->source === 'manual' ? 'warning' : 'success' }}">{{ ucfirst($listing->source) }}</span>
                                    <span>{{ $listing->collected_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 ml-4">
                                <x-price-display :value="$listing->price" size="small" color="green" />
                                @if($listing->url)
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-apple-muted flex-shrink-0">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </x-card>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $listings->withQueryString()->links() }}
        </div>
    @endif

    <script>
        function marketRadarFilter() {
            return {
                modelsMap: @json($models),
                selectedModel: '{{ request('model', '') }}',
                storageVisible(storage) {
                    if (!this.selectedModel || !this.modelsMap[this.selectedModel]) return true;
                    return this.modelsMap[this.selectedModel].includes(storage);
                },
                onModelChange() {
                    const sel = this.$refs.storageSelect;
                    if (sel.value && !this.storageVisible(sel.value)) {
                        sel.value = '';
                    }
                }
            }
        }
    </script>
</x-layouts.app>

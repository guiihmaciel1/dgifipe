<x-layouts.app>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-apple-text tracking-tight">Avaliador de iPhone</h1>
            <p class="text-apple-muted mt-1">Descubra o preço ideal de compra.</p>
        </div>
    </div>

    <div x-data="evaluator()" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Input Panel --}}
        <x-card>
            <div class="space-y-5">
                <div>
                    <label class="label">Modelo</label>
                    <div class="relative" x-data="{ open: false, search: '' }">
                        <input type="text" x-model="search" @focus="open = true" @click.away="open = false"
                               :placeholder="selectedModelLabel || 'Buscar modelo...'"
                               class="input-field">
                        <div x-show="open" x-transition
                             class="absolute z-20 mt-1 w-full bg-white rounded-apple-lg shadow-apple-lg max-h-60 overflow-y-auto">
                            <template x-for="(storages, name) in filteredModels(search)" :key="name">
                                <button type="button" @click="selectModel(name); open = false; search = ''"
                                        class="w-full px-4 py-2.5 text-left text-sm hover:bg-apple-bg transition-colors"
                                        :class="{ 'text-apple-blue font-medium': form.model === name }"
                                        x-text="name"></button>
                            </template>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="label">Armazenamento</label>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="s in availableStorages" :key="s">
                            <button type="button" @click="form.storage = s"
                                    class="px-4 py-2 rounded-apple text-sm font-medium transition-all duration-200"
                                    :class="form.storage === s
                                        ? 'bg-apple-blue text-white shadow-sm'
                                        : 'bg-apple-bg text-apple-text hover:bg-gray-200'"
                                    x-text="s"></button>
                        </template>
                    </div>
                </div>

                <div>
                    <label class="label">
                        Saúde da Bateria: <span x-text="form.battery_health + '%'" class="text-apple-text"></span>
                        <span class="ml-2 text-xs font-normal px-2 py-0.5 rounded-full"
                              :class="batteryBadgeClass"
                              x-text="batteryLabel"></span>
                    </label>
                    <input type="range" x-model.number="form.battery_health" min="0" max="100" step="1"
                           class="w-full h-2 bg-apple-bg rounded-full appearance-none cursor-pointer accent-apple-blue">
                    <div class="flex justify-between text-xs text-apple-muted mt-1">
                        <span>0%</span>
                        <span>100%</span>
                    </div>
                </div>

                <div>
                    <label class="label">Estado do Aparelho</label>
                    <div class="flex gap-2">
                        <button type="button" @click="form.device_state = 'original'"
                                class="flex-1 px-4 py-2.5 rounded-apple text-sm font-medium transition-all duration-200"
                                :class="form.device_state === 'original'
                                    ? 'bg-apple-blue text-white shadow-sm'
                                    : 'bg-apple-bg text-apple-text hover:bg-gray-200'">
                            Original
                        </button>
                        <button type="button" @click="form.device_state = 'repaired'"
                                class="flex-1 px-4 py-2.5 rounded-apple text-sm font-medium transition-all duration-200"
                                :class="form.device_state === 'repaired'
                                    ? 'bg-apple-blue text-white shadow-sm'
                                    : 'bg-apple-bg text-apple-text hover:bg-gray-200'">
                            Já foi aberto / trocou peça
                        </button>
                    </div>
                </div>

                <div>
                    <label class="label">
                        Acessórios
                        <span class="ml-2 text-xs font-normal px-2 py-0.5 rounded-full"
                              :class="accessoryBadgeClass"
                              x-text="accessoryLabel"></span>
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 p-3 rounded-apple bg-apple-bg cursor-pointer hover:bg-gray-100 transition-colors">
                            <input type="checkbox" x-model="form.no_box"
                                   class="w-5 h-5 rounded border-apple-separator text-apple-blue focus:ring-apple-blue">
                            <span class="text-sm text-apple-text">Sem caixa</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-apple bg-apple-bg cursor-pointer hover:bg-gray-100 transition-colors">
                            <input type="checkbox" x-model="form.no_cable"
                                   class="w-5 h-5 rounded border-apple-separator text-apple-blue focus:ring-apple-blue">
                            <span class="text-sm text-apple-text">Sem cabo</span>
                        </label>
                    </div>
                </div>

                {{-- Loading indicator --}}
                <div x-show="loading" x-transition class="flex items-center justify-center gap-2 text-sm text-apple-muted py-2">
                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Calculando...
                </div>
            </div>
        </x-card>

        {{-- Results Panel --}}
        <div>
            <template x-if="result">
                <div class="space-y-4">
                    {{-- Preço Sugerido de Compra --}}
                    <x-card>
                        <p class="label">Preço Sugerido de Compra</p>
                        <p class="price-accent" x-text="formatPrice(result.suggested_price)"></p>
                        <div class="flex items-center justify-between mt-2">
                            <p class="text-sm text-apple-muted">Valor ideal para adquirir este aparelho.</p>
                            <a :href="marketRadarUrl" target="_blank"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-apple text-xs font-medium bg-apple-bg text-apple-muted hover:bg-gray-200 hover:text-apple-text transition-colors shrink-0 ml-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                                </svg>
                                Market Radar
                            </a>
                        </div>
                    </x-card>

                    {{-- Preço Sugerido de Revenda --}}
                    <x-card>
                        <p class="label">Preço Sugerido de Revenda</p>
                        <p class="text-3xl font-bold text-apple-blue tracking-tight" x-text="formatPrice(result.resale_price)"></p>
                        <p class="text-sm text-apple-muted mt-2">Margem de <span x-text="result.resale_margin + '%'"></span> sobre a compra.</p>
                    </x-card>

                    {{-- Barra de Desconto --}}
                    <x-card>
                        <p class="label mb-3">Desconto Aplicado: <span class="text-apple-text font-semibold" x-text="totalDiscount.toFixed(1) + '%'"></span></p>
                        <div class="relative h-3 rounded-full overflow-hidden" style="background: linear-gradient(to right, #34c759, #ffcc00, #ff3b30);">
                            <div class="absolute top-1/2 -translate-y-1/2 w-4 h-4 rounded-full bg-white border-2 border-apple-text shadow-md transition-all duration-300"
                                 :style="'left: calc(' + discountBarPosition + '% - 8px)'"></div>
                        </div>
                        <div class="flex justify-between text-[10px] text-apple-muted mt-1">
                            <span>12%</span>
                            <span>43%</span>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-3">
                            <span class="text-xs px-2 py-1 rounded-full bg-apple-bg text-apple-muted">Margem <span x-text="result.margin + '%'"></span></span>
                            <span class="text-xs px-2 py-1 rounded-full" :class="modifierChipClass(result.battery_modifier)">Bateria <span x-text="formatModifier(result.battery_modifier)"></span></span>
                            <span class="text-xs px-2 py-1 rounded-full" :class="modifierChipClass(result.device_state_modifier)">Estado <span x-text="formatModifier(result.device_state_modifier)"></span></span>
                            <span class="text-xs px-2 py-1 rounded-full" :class="modifierChipClass(result.accessory_modifier)">Acessórios <span x-text="formatModifier(result.accessory_modifier)"></span></span>
                        </div>
                    </x-card>

                    {{-- Média e Mediana --}}
                    <div class="grid grid-cols-2 gap-4">
                        <x-card>
                            <p class="label">Média do Mercado</p>
                            <p class="text-2xl font-semibold text-apple-text" x-text="formatPrice(result.market_average)"></p>
                        </x-card>
                        <x-card>
                            <p class="label">Mediana</p>
                            <p class="text-2xl font-semibold text-apple-text" x-text="formatPrice(result.median)"></p>
                        </x-card>
                    </div>

                    {{-- Menor/Maior --}}
                    <div class="grid grid-cols-2 gap-4">
                        <x-card>
                            <p class="label">Menor Preço</p>
                            <p class="text-xl font-semibold text-apple-text" x-text="formatPrice(result.price_min)"></p>
                        </x-card>
                        <x-card>
                            <p class="label">Maior Preço</p>
                            <p class="text-xl font-semibold text-apple-text" x-text="formatPrice(result.price_max)"></p>
                        </x-card>
                    </div>

                    {{-- Anúncios + Confiança + Data --}}
                    <x-card>
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="label">Anúncios Analisados</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <p class="text-xl font-semibold text-apple-text" x-text="result.listings_count"></p>
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-full"
                                          :class="confidenceBadgeClass"
                                          x-text="confidenceLabel"></span>
                                </div>
                            </div>
                            <div class="text-right text-xs text-apple-muted space-y-0.5">
                                <template x-if="result.last_collected_at">
                                    <p :class="isDataOld ? 'text-apple-orange font-medium' : ''">
                                        <span x-show="isDataOld">⚠ </span>Dados de <span x-text="formatDate(result.last_collected_at)"></span>
                                    </p>
                                </template>
                            </div>
                        </div>
                    </x-card>

                    <div x-show="result.low_data_warning"
                         class="p-4 rounded-apple-lg bg-apple-orange/10 text-apple-orange text-sm font-medium">
                        Poucos anúncios encontrados. O resultado pode não ser preciso.
                    </div>

                    {{-- Histórico inline --}}
                    <template x-if="result.last_evaluation">
                        <div class="text-center text-sm text-apple-muted py-2">
                            Última avaliação deste modelo:
                            <span class="font-medium text-apple-text" x-text="formatPrice(result.last_evaluation.suggested_price)"></span>
                            <span x-text="result.last_evaluation.date === 'hoje' ? 'hoje' : 'em ' + result.last_evaluation.date"></span>
                            <span x-show="result.last_evaluation.days_ago >= 2" x-text="'(' + result.last_evaluation.days_ago + ' dias atrás)'"></span>
                            <span x-show="result.last_evaluation.days_ago === 1">(ontem)</span>
                            · <a href="{{ route('history') }}" class="text-apple-blue hover:underline">Ver histórico</a>
                        </div>
                    </template>

                    {{-- Compartilhar --}}
                    <div class="flex justify-center pt-2">
                        <button type="button" @click="shareOpen = true"
                                class="text-sm text-apple-muted hover:text-apple-blue font-medium flex items-center gap-1.5 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.935-2.186 2.25 2.25 0 0 0-3.935 2.186Z"/>
                            </svg>
                            Compartilhar Avaliação
                        </button>
                    </div>
                </div>
            </template>

            <template x-if="error">
                <x-card>
                    <p class="text-center text-apple-red py-8" x-text="error"></p>
                </x-card>
            </template>

            <template x-if="!result && !error && !loading">
                <x-card>
                    <div class="text-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-apple-separator mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/>
                        </svg>
                        <p class="text-apple-muted">Selecione um modelo para avaliar automaticamente.</p>
                    </div>
                </x-card>
            </template>
        </div>

        {{-- Modal Compartilhar --}}
        <template x-if="shareOpen">
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="shareOpen = false">
                <div class="absolute inset-0 bg-black/40" @click="shareOpen = false"></div>
                <div class="relative bg-white rounded-apple-xl shadow-apple-lg max-w-md w-full p-6 space-y-4" @click.stop>
                    <div id="share-card" class="space-y-3">
                        <div class="flex items-center gap-2 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-apple-text" viewBox="0 0 814 1000" fill="currentColor">
                                <path d="M788.1 340.9c-5.8 4.5-108.2 62.2-108.2 190.5 0 148.4 130.3 200.9 134.2 202.2-.6 3.2-20.7 71.9-68.7 141.9-42.8 61.6-87.5 123.1-155.5 123.1s-85.5-39.5-164-39.5c-76.5 0-103.7 40.8-165.9 40.8s-105.6-57.8-155.5-127.4c-58.3-81.3-105.9-207.9-105.9-328.5 0-193.1 125.4-295.7 248.8-295.7 65.6 0 120.3 43.1 161.4 43.1 39.1 0 100.1-45.7 174.5-45.7 28.2 0 129.5 2.6 196.8 99.2zM554.1 159.4c31.1-36.9 53.1-88.1 53.1-139.3 0-7.1-.6-14.3-1.9-20.1-50.6 1.9-110.8 33.7-147.1 75.8-28.5 32.4-55.1 83.6-55.1 135.5 0 7.8 1.3 15.6 1.9 18.1 3.2.6 8.4 1.3 13.6 1.3 45.4 0 103.5-30.4 135.5-71.3z"/>
                            </svg>
                            <span class="font-semibold text-apple-text">DG iFIPE</span>
                        </div>
                        <div class="border-t border-apple-separator pt-3">
                            <p class="text-lg font-semibold text-apple-text" x-text="form.model + ' ' + form.storage"></p>
                            <div class="flex flex-wrap gap-2 mt-2 text-xs">
                                <span class="px-2 py-0.5 rounded-full bg-apple-bg text-apple-muted" x-text="'Bateria ' + form.battery_health + '%'"></span>
                                <span class="px-2 py-0.5 rounded-full bg-apple-bg text-apple-muted" x-text="form.device_state === 'original' ? 'Original' : 'Reparado'"></span>
                                <span class="px-2 py-0.5 rounded-full bg-apple-bg text-apple-muted" x-text="accessoryLabel"></span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <div class="bg-apple-bg rounded-apple p-3">
                                <p class="text-[10px] text-apple-muted uppercase tracking-wider">Compra</p>
                                <p class="text-lg font-bold text-apple-green" x-text="formatPrice(result?.suggested_price)"></p>
                            </div>
                            <div class="bg-apple-bg rounded-apple p-3">
                                <p class="text-[10px] text-apple-muted uppercase tracking-wider">Revenda</p>
                                <p class="text-lg font-bold text-apple-blue" x-text="formatPrice(result?.resale_price)"></p>
                            </div>
                        </div>
                        <div class="text-center pt-1">
                            <p class="text-[10px] text-apple-muted">Média do mercado: <span x-text="formatPrice(result?.market_average)"></span> · <span x-text="result?.listings_count"></span> anúncios</p>
                            <p class="text-[10px] text-apple-muted" x-text="shareDateTime"></p>
                        </div>
                    </div>
                    <div class="flex gap-3 pt-2 border-t border-apple-separator">
                        <button type="button" @click="copyShareText()"
                                class="flex-1 btn-primary text-sm flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9.75a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184"/>
                            </svg>
                            <span x-text="copied ? 'Copiado!' : 'Copiar'"></span>
                        </button>
                        <button type="button" @click="printShare()"
                                class="flex-1 py-2 px-4 rounded-apple text-sm font-medium bg-apple-bg text-apple-text hover:bg-gray-200 transition-colors flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659"/>
                            </svg>
                            Imprimir
                        </button>
                    </div>
                    <button type="button" @click="shareOpen = false"
                            class="absolute top-3 right-3 p-1 rounded-apple text-apple-muted hover:text-apple-text transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </template>
    </div>

    <style>
        @media print {
            body > *:not(.print-share) { display: none !important; }
            .print-share { display: block !important; }
        }
    </style>

    <script>
        function evaluator() {
            return {
                models: @json($models),
                form: {
                    model: '',
                    storage: '',
                    battery_health: 100,
                    device_state: 'original',
                    no_box: false,
                    no_cable: false,
                },
                result: null,
                error: null,
                loading: false,
                shareOpen: false,
                copied: false,
                _debounceTimer: null,

                init() {
                    const fields = ['form.model', 'form.storage', 'form.battery_health', 'form.device_state', 'form.no_box', 'form.no_cable'];
                    fields.forEach(field => {
                        this.$watch(field, () => this.debouncedCalculate());
                    });
                },

                debouncedCalculate() {
                    clearTimeout(this._debounceTimer);
                    this._debounceTimer = setTimeout(() => this.calculate(), 500);
                },

                get selectedModelLabel() {
                    return this.form.model || '';
                },

                get availableStorages() {
                    return this.models[this.form.model] || [];
                },

                get batteryLabel() {
                    if (this.form.battery_health >= 90) return 'Excelente';
                    if (this.form.battery_health >= 80) return 'Bom';
                    if (this.form.battery_health >= 70) return 'Regular';
                    return 'Ruim';
                },

                get batteryBadgeClass() {
                    if (this.form.battery_health >= 90) return 'bg-green-100 text-green-700';
                    if (this.form.battery_health >= 80) return 'bg-yellow-100 text-yellow-700';
                    if (this.form.battery_health >= 70) return 'bg-orange-100 text-orange-700';
                    return 'bg-red-100 text-red-700';
                },

                get accessoryLabel() {
                    const count = (this.form.no_box ? 1 : 0) + (this.form.no_cable ? 1 : 0);
                    if (count === 0) return 'Completo (+3%)';
                    if (count === 1) return 'Parcial (0%)';
                    return 'Nenhum (-3%)';
                },

                get accessoryBadgeClass() {
                    const count = (this.form.no_box ? 1 : 0) + (this.form.no_cable ? 1 : 0);
                    if (count === 0) return 'bg-green-100 text-green-700';
                    if (count === 1) return 'bg-yellow-100 text-yellow-700';
                    return 'bg-red-100 text-red-700';
                },

                get totalDiscount() {
                    if (!this.result) return 0;
                    const mods = this.result.battery_modifier + this.result.device_state_modifier + this.result.accessory_modifier;
                    return this.result.margin - mods;
                },

                get discountBarPosition() {
                    const pct = ((this.totalDiscount - 12) / (43 - 12)) * 100;
                    return Math.max(0, Math.min(100, pct));
                },

                get confidenceLabel() {
                    if (!this.result) return '';
                    const map = { high: 'Alta', medium: 'Média', low: 'Baixa' };
                    return 'Confiança ' + (map[this.result.confidence] || 'Baixa');
                },

                get confidenceBadgeClass() {
                    if (!this.result) return '';
                    const map = {
                        high: 'bg-green-100 text-green-700',
                        medium: 'bg-yellow-100 text-yellow-700',
                        low: 'bg-red-100 text-red-700',
                    };
                    return map[this.result.confidence] || map.low;
                },

                get isDataOld() {
                    if (!this.result?.last_collected_at) return false;
                    const collected = new Date(this.result.last_collected_at);
                    const now = new Date();
                    const diffDays = (now - collected) / (1000 * 60 * 60 * 24);
                    return diffDays > 3;
                },

                get marketRadarUrl() {
                    const base = '{{ route("market-radar") }}';
                    const params = new URLSearchParams();
                    if (this.form.model) params.set('model', this.form.model);
                    if (this.form.storage) params.set('storage', this.form.storage);
                    return base + '?' + params.toString();
                },

                get shareDateTime() {
                    const now = new Date();
                    return now.toLocaleDateString('pt-BR') + ' às ' + now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
                },

                filteredModels(search) {
                    if (!search) return this.models;
                    const s = search.toLowerCase();
                    return Object.fromEntries(
                        Object.entries(this.models).filter(([k]) => k.toLowerCase().includes(s))
                    );
                },

                selectModel(name) {
                    this.form.model = name;
                    const storages = this.models[name] || [];
                    if (!storages.includes(this.form.storage)) {
                        this.form.storage = storages[0] || '';
                    }
                },

                resetForm() {
                    this.form.model = '';
                    this.form.storage = '';
                    this.form.battery_health = 100;
                    this.form.device_state = 'original';
                    this.form.no_box = false;
                    this.form.no_cable = false;
                    this.result = null;
                    this.error = null;
                },

                formatPrice(val) {
                    if (val === null || val === undefined) return '—';
                    return 'R$ ' + Number(val).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                },

                formatModifier(val) {
                    if (val > 0) return '+' + val + '%';
                    return val + '%';
                },

                formatDate(dateStr) {
                    if (!dateStr) return '';
                    const d = new Date(dateStr);
                    return d.toLocaleDateString('pt-BR');
                },

                modifierChipClass(val) {
                    if (val > 0) return 'bg-green-100 text-green-700';
                    if (val < 0) return 'bg-red-100 text-red-700';
                    return 'bg-apple-bg text-apple-muted';
                },

                async copyShareText() {
                    const text = `DG iFIPE - Avaliação\n${this.form.model} ${this.form.storage}\nBateria: ${this.form.battery_health}%\nEstado: ${this.form.device_state === 'original' ? 'Original' : 'Reparado'}\nAcessórios: ${this.accessoryLabel}\n\nCompra: ${this.formatPrice(this.result?.suggested_price)}\nRevenda: ${this.formatPrice(this.result?.resale_price)}\nMédia mercado: ${this.formatPrice(this.result?.market_average)}\n${this.result?.listings_count} anúncios analisados\n${this.shareDateTime}`;
                    try {
                        await navigator.clipboard.writeText(text);
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2000);
                    } catch (e) {}
                },

                printShare() {
                    const card = document.getElementById('share-card');
                    if (!card) return;
                    const w = window.open('', '_blank', 'width=400,height=600');
                    w.document.write(`<html><head><title>DG iFIPE</title><style>body{font-family:-apple-system,BlinkMacSystemFont,sans-serif;padding:24px;color:#1d1d1f}p{margin:4px 0}.label{font-size:10px;color:#86868b;text-transform:uppercase;letter-spacing:0.5px}.value{font-size:18px;font-weight:700}.green{color:#34c759}.blue{color:#007aff}</style></head><body>`);
                    w.document.write(`<h2>DG iFIPE</h2>`);
                    w.document.write(`<p style="font-size:16px;font-weight:600">${this.form.model} ${this.form.storage}</p>`);
                    w.document.write(`<p>Bateria: ${this.form.battery_health}% · ${this.form.device_state === 'original' ? 'Original' : 'Reparado'} · ${this.accessoryLabel}</p><hr>`);
                    w.document.write(`<p class="label">Preço de Compra</p><p class="value green">${this.formatPrice(this.result?.suggested_price)}</p>`);
                    w.document.write(`<p class="label">Preço de Revenda</p><p class="value blue">${this.formatPrice(this.result?.resale_price)}</p>`);
                    w.document.write(`<p class="label">Média do Mercado</p><p>${this.formatPrice(this.result?.market_average)}</p>`);
                    w.document.write(`<p style="font-size:11px;color:#86868b;margin-top:12px">${this.result?.listings_count} anúncios · ${this.shareDateTime}</p>`);
                    w.document.write(`</body></html>`);
                    w.document.close();
                    w.print();
                },

                async calculate() {
                    if (!this.form.model || !this.form.storage) return;

                    this.loading = true;
                    this.error = null;

                    try {
                        const res = await fetch('{{ route("evaluator.calculate") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(this.form),
                        });

                        const data = await res.json();

                        if (!res.ok) {
                            this.error = data.message || 'Erro ao calcular.';
                            this.result = null;
                            return;
                        }

                        if (data.listings_count === 0) {
                            this.error = data.message || 'Nenhum anúncio encontrado.';
                            this.result = null;
                            return;
                        }

                        this.result = data;
                    } catch (e) {
                        this.error = 'Erro de conexão. Tente novamente.';
                        this.result = null;
                    } finally {
                        this.loading = false;
                    }
                },
            };
        }
    </script>
</x-layouts.app>

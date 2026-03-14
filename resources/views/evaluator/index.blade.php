<x-layouts.app>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-apple-text tracking-tight">Avaliador de iPhone</h1>
        <p class="text-apple-muted mt-1">Descubra o preço ideal de compra.</p>
    </div>

    <div x-data="evaluator()" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Input Panel --}}
        <x-card>
            <form @submit.prevent="calculate" class="space-y-5">
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

                <button type="submit" class="btn-primary w-full" :disabled="loading">
                    <span x-show="!loading">Avaliar</span>
                    <span x-show="loading" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Calculando...
                    </span>
                </button>
            </form>
        </x-card>

        {{-- Results Panel --}}
        <div>
            <template x-if="result">
                <div class="space-y-4">
                    <x-card>
                        <p class="label">Preço Sugerido de Compra</p>
                        <p class="price-accent" x-text="formatPrice(result.suggested_price)"></p>
                        <p class="text-sm text-apple-muted mt-2">Valor ideal para adquirir este aparelho.</p>
                    </x-card>

                    <x-card>
                        <p class="label">Média do Mercado</p>
                        <p class="text-2xl font-semibold text-apple-text" x-text="formatPrice(result.market_average)"></p>
                    </x-card>

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

                    <x-card>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="label">Anúncios Analisados</p>
                                <p class="text-xl font-semibold text-apple-text" x-text="result.listings_count"></p>
                            </div>
                            <div class="text-right text-sm text-apple-muted space-y-0.5">
                                <p>Margem base: <span x-text="result.margin + '%'"></span></p>
                                <p>Bateria: <span x-text="formatModifier(result.battery_modifier)"></span></p>
                                <p>Estado: <span x-text="formatModifier(result.device_state_modifier)"></span></p>
                                <p>Acessórios: <span x-text="formatModifier(result.accessory_modifier)"></span></p>
                            </div>
                        </div>
                    </x-card>

                    <div x-show="result.low_data_warning"
                         class="p-4 rounded-apple-lg bg-apple-orange/10 text-apple-orange text-sm font-medium">
                        Poucos anúncios encontrados. O resultado pode não ser preciso.
                    </div>
                </div>
            </template>

            <template x-if="error">
                <x-card>
                    <p class="text-center text-apple-red py-8" x-text="error"></p>
                </x-card>
            </template>

            <template x-if="!result && !error">
                <x-card>
                    <div class="text-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-apple-separator mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/>
                        </svg>
                        <p class="text-apple-muted">Selecione um modelo e clique em <strong>Avaliar</strong>.</p>
                    </div>
                </x-card>
            </template>
        </div>
    </div>

    <script>
        function evaluator() {
            return {
                models: @json($models),
                form: {
                    model: 'iPhone 11',
                    storage: '64GB',
                    battery_health: 100,
                    device_state: 'original',
                    no_box: false,
                    no_cable: false,
                },
                result: null,
                error: null,
                loading: false,

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

                formatPrice(val) {
                    if (val === null || val === undefined) return '—';
                    return 'R$ ' + Number(val).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                },

                formatModifier(val) {
                    if (val > 0) return '+' + val + '%';
                    return val + '%';
                },

                async calculate() {
                    this.loading = true;
                    this.error = null;
                    this.result = null;

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
                            return;
                        }

                        if (data.listings_count === 0) {
                            this.error = data.message || 'Nenhum anúncio encontrado.';
                            return;
                        }

                        this.result = data;
                    } catch (e) {
                        this.error = 'Erro de conexão. Tente novamente.';
                    } finally {
                        this.loading = false;
                    }
                },
            };
        }
    </script>
</x-layouts.app>

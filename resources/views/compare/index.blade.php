<x-layouts.app>
    {{-- Mobile: mensagem de indisponibilidade --}}
    <div class="lg:hidden flex flex-col items-center justify-center py-20 px-6 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-apple-separator mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25"/>
        </svg>
        <h2 class="text-xl font-semibold text-apple-text mb-2">Comparador disponível no desktop</h2>
        <p class="text-apple-muted text-sm max-w-xs">Para comparar modelos de iPhone lado a lado, acesse pelo computador.</p>
    </div>

    {{-- Desktop: comparador --}}
    <div class="hidden lg:block" x-data="comparator()">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-apple-text tracking-tight">Comparar iPhones</h1>
            <p class="text-apple-muted mt-1">Selecione até 3 modelos para comparar lado a lado.</p>
        </div>

        {{-- Seletores --}}
        <div class="grid grid-cols-3 gap-6 mb-8">
            <template x-for="(sel, idx) in selected" :key="idx">
                <div class="relative" x-data="{ open: false, search: '' }">
                    <div class="flex items-center gap-2">
                        <input type="text"
                               x-model="search"
                               @focus="open = true"
                               @click.away="open = false"
                               :placeholder="sel ? sel : ('Selecionar iPhone ' + (idx + 1) + '...')"
                               class="input-field text-sm">
                        <button x-show="sel" @click="clearSlot(idx); search = ''"
                                class="p-2 rounded-apple text-apple-muted hover:text-apple-red hover:bg-apple-red/10 transition-colors shrink-0"
                                type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div x-show="open" x-transition
                         class="absolute z-30 mt-1 w-full bg-white rounded-apple-lg shadow-apple-lg max-h-64 overflow-y-auto">
                        <template x-for="name in filteredModels(search)" :key="name">
                            <button type="button"
                                    @click="selectModel(idx, name); open = false; search = ''"
                                    class="w-full px-4 py-2.5 text-left text-sm hover:bg-apple-bg transition-colors"
                                    :class="{
                                        'text-apple-blue font-medium': sel === name,
                                        'opacity-40 cursor-not-allowed': isAlreadySelected(name, idx)
                                    }"
                                    :disabled="isAlreadySelected(name, idx)"
                                    x-text="name"></button>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        {{-- Tabela Comparativa --}}
        <template x-if="hasSelection">
            <div class="space-y-6">
                {{-- Header com nomes --}}
                <div class="grid gap-6" :class="gridCols">
                    <template x-for="(sel, idx) in activeSelections" :key="idx">
                        <x-card class="text-center">
                            <p class="text-lg font-semibold text-apple-text" x-text="sel"></p>
                            <p class="text-xs text-apple-muted mt-1" x-text="getSpec(sel, 'variant') + ' · ' + getSpec(sel, 'year')"></p>
                        </x-card>
                    </template>
                </div>

                {{-- Seções de specs --}}
                <template x-for="section in sections" :key="section.title">
                    <div>
                        <h3 class="text-sm font-semibold text-apple-muted uppercase tracking-wider mb-3 px-1" x-text="section.title"></h3>
                        <div class="bg-white rounded-apple-xl shadow-apple overflow-hidden divide-y divide-apple-separator/50">
                            <template x-for="field in section.fields" :key="field.key">
                                <div>
                                    {{-- Label da linha --}}
                                    <div class="px-6 pt-3 pb-1">
                                        <p class="text-[11px] font-medium text-apple-muted uppercase tracking-wider text-center" x-text="field.label"></p>
                                    </div>
                                    {{-- Valores --}}
                                    <div class="grid items-center gap-6 px-6 pb-3.5"
                                         :class="[gridCols, isDifferent(field.key) ? 'bg-blue-50/60' : '']">
                                        <template x-for="(sel, idx) in activeSelections" :key="idx">
                                            <div class="text-center">
                                                <template x-if="field.type === 'boolean'">
                                                    <span class="inline-flex items-center gap-1 text-sm font-medium"
                                                          :class="getSpec(sel, field.key) ? 'text-apple-green' : 'text-apple-muted'">
                                                        <svg x-show="getSpec(sel, field.key)" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <svg x-show="!getSpec(sel, field.key)" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/>
                                                        </svg>
                                                        <span x-text="getSpec(sel, field.key) ? 'Sim' : 'Não'"></span>
                                                    </span>
                                                </template>
                                                <template x-if="field.type === 'storage'">
                                                    <div class="flex flex-wrap justify-center gap-1">
                                                        <template x-for="s in getSpec(sel, field.key)" :key="s">
                                                            <span class="badge bg-apple-bg text-apple-text" x-text="s"></span>
                                                        </template>
                                                    </div>
                                                </template>
                                                <template x-if="field.type === 'currency'">
                                                    <span class="text-sm font-semibold text-apple-text"
                                                          x-text="'R$ ' + Number(getSpec(sel, field.key)).toLocaleString('pt-BR')"></span>
                                                </template>
                                                <template x-if="field.type === 'weight'">
                                                    <span class="text-sm font-medium"
                                                          :class="isBest(sel, field.key, 'min') ? 'text-apple-green font-semibold' : 'text-apple-text'"
                                                          x-text="getSpec(sel, field.key) + ' g'"></span>
                                                </template>
                                                <template x-if="field.type === 'hours'">
                                                    <span class="text-sm font-medium"
                                                          :class="isBest(sel, field.key, 'max') ? 'text-apple-green font-semibold' : 'text-apple-text'"
                                                          x-text="getSpec(sel, field.key) + 'h'"></span>
                                                </template>
                                                <template x-if="field.type === 'numeric'">
                                                    <span class="text-sm font-medium"
                                                          :class="isBest(sel, field.key, 'max') ? 'text-apple-green font-semibold' : 'text-apple-text'"
                                                          x-text="getSpec(sel, field.key) + (field.suffix || '')"></span>
                                                </template>
                                                <template x-if="!field.type || field.type === 'text'">
                                                    <span class="text-sm text-apple-text" x-text="getSpec(sel, field.key)"></span>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        {{-- Estado vazio --}}
        <template x-if="!hasSelection">
            <x-card>
                <div class="text-center py-16">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-apple-separator mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                    </svg>
                    <p class="text-apple-muted">Selecione pelo menos 2 modelos acima para comparar.</p>
                </div>
            </x-card>
        </template>
    </div>

    <script>
        function comparator() {
            return {
                allSpecs: @json($specs),
                allModels: @json($models),
                selected: [null, null, null],

                sections: [
                    {
                        title: 'Tela',
                        fields: [
                            { key: 'screen_size', label: 'Tamanho', type: 'text' },
                            { key: 'screen_type', label: 'Tipo', type: 'text' },
                            { key: 'resolution', label: 'Resolução', type: 'text' },
                            { key: 'refresh_rate', label: 'Taxa de Atualização', type: 'text' },
                            { key: 'dynamic_island', label: 'Dynamic Island', type: 'boolean' },
                            { key: 'always_on', label: 'Always-On Display', type: 'boolean' },
                        ]
                    },
                    {
                        title: 'Desempenho',
                        fields: [
                            { key: 'chip', label: 'Chip', type: 'text' },
                            { key: 'ram', label: 'Memória RAM', type: 'text' },
                        ]
                    },
                    {
                        title: 'Câmera',
                        fields: [
                            { key: 'rear_camera', label: 'Câmera Traseira', type: 'text' },
                            { key: 'rear_megapixels', label: 'Megapixels', type: 'numeric', suffix: ' MP' },
                            { key: 'optical_zoom', label: 'Zoom Óptico', type: 'text' },
                            { key: 'front_camera', label: 'Câmera Frontal', type: 'text' },
                            { key: 'prores', label: 'ProRes', type: 'boolean' },
                            { key: 'lidar', label: 'LiDAR', type: 'boolean' },
                        ]
                    },
                    {
                        title: 'Bateria e Carregamento',
                        fields: [
                            { key: 'video_hours', label: 'Reprodução de Vídeo', type: 'hours' },
                            { key: 'connector', label: 'Conector', type: 'text' },
                            { key: 'usb_speed', label: 'Velocidade USB', type: 'text' },
                        ]
                    },
                    {
                        title: 'Conectividade',
                        fields: [
                            { key: 'five_g', label: '5G', type: 'boolean' },
                            { key: 'wifi', label: 'Wi-Fi', type: 'text' },
                            { key: 'bluetooth', label: 'Bluetooth', type: 'text' },
                            { key: 'nfc', label: 'NFC', type: 'boolean' },
                        ]
                    },
                    {
                        title: 'Armazenamento',
                        fields: [
                            { key: 'storage_options', label: 'Opções', type: 'storage' },
                        ]
                    },
                    {
                        title: 'Recursos',
                        fields: [
                            { key: 'action_button', label: 'Action Button', type: 'boolean' },
                            { key: 'camera_control', label: 'Camera Control', type: 'boolean' },
                            { key: 'ceramic_shield', label: 'Ceramic Shield', type: 'boolean' },
                            { key: 'magsafe', label: 'MagSafe', type: 'boolean' },
                        ]
                    },
                    {
                        title: 'Físico',
                        fields: [
                            { key: 'weight', label: 'Peso', type: 'weight' },
                            { key: 'material', label: 'Material', type: 'text' },
                            { key: 'water_resistance', label: 'Resistência à Água', type: 'text' },
                        ]
                    },
                    {
                        title: 'Lançamento',
                        fields: [
                            { key: 'year', label: 'Ano', type: 'text' },
                            { key: 'launch_price_brl', label: 'Preço de Lançamento', type: 'currency' },
                        ]
                    },
                ],

                get activeSelections() {
                    return this.selected.filter(s => s !== null);
                },

                get hasSelection() {
                    return this.activeSelections.length >= 2;
                },

                get gridCols() {
                    const count = this.activeSelections.length;
                    if (count === 3) return 'grid-cols-3';
                    if (count === 2) return 'grid-cols-2';
                    return 'grid-cols-1';
                },

                filteredModels(search) {
                    if (!search) return this.allModels;
                    const s = search.toLowerCase();
                    return this.allModels.filter(m => m.toLowerCase().includes(s));
                },

                isAlreadySelected(name, currentIdx) {
                    return this.selected.some((s, i) => i !== currentIdx && s === name);
                },

                selectModel(idx, name) {
                    this.selected[idx] = name;
                },

                clearSlot(idx) {
                    this.selected[idx] = null;
                },

                getSpec(model, key) {
                    if (!model || !this.allSpecs[model]) return '';
                    return this.allSpecs[model][key] ?? '';
                },

                isDifferent(key) {
                    const vals = this.activeSelections.map(m => JSON.stringify(this.getSpec(m, key)));
                    const unique = [...new Set(vals)];
                    return unique.length > 1;
                },

                isBest(model, key, direction) {
                    if (this.activeSelections.length < 2) return false;
                    const val = this.getSpec(model, key);
                    if (typeof val !== 'number') return false;

                    const allVals = this.activeSelections.map(m => this.getSpec(m, key));
                    if (!this.isDifferent(key)) return false;

                    if (direction === 'max') return val === Math.max(...allVals);
                    if (direction === 'min') return val === Math.min(...allVals);
                    return false;
                },
            };
        }
    </script>
</x-layouts.app>

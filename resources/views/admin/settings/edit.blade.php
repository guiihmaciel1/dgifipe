<x-layouts.app>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-apple-text tracking-tight">Configurações do Avaliador</h1>
        <p class="text-apple-muted mt-1">{{ $company->name }}</p>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-apple-lg bg-green-50 text-green-700 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6 max-w-2xl">
        @csrf
        @method('PUT')

        {{-- Margem Base --}}
        <x-card>
            <h3 class="text-base font-semibold text-apple-text mb-4">Margem Base</h3>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-apple-text">Desconto fixo sobre o preço de mercado</p>
                    <p class="text-xs text-apple-muted">Aplicado em todas as avaliações</p>
                </div>
                <div class="flex items-center gap-1">
                    <input type="number" name="default_margin" step="0.5" min="0" max="50"
                           value="{{ old('default_margin', $settings->default_margin ?? 15) }}"
                           class="input-field w-20 text-right" required>
                    <span class="text-sm text-apple-muted">%</span>
                </div>
            </div>
            @error('default_margin') <p class="mt-1 text-xs text-apple-red">{{ $message }}</p> @enderror
        </x-card>

        {{-- Margem de Revenda --}}
        <x-card>
            <h3 class="text-base font-semibold text-apple-text mb-4">Margem de Revenda</h3>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-apple-text">Margem sobre o preço de compra</p>
                    <p class="text-xs text-apple-muted">Para sugerir o preço de revenda ao atendente</p>
                </div>
                <div class="flex items-center gap-1">
                    <input type="number" name="resale_margin" step="0.5" min="0" max="100"
                           value="{{ old('resale_margin', $settings->resale_margin ?? 20) }}"
                           class="input-field w-20 text-right" required>
                    <span class="text-sm text-apple-muted">%</span>
                </div>
            </div>
            @error('resale_margin') <p class="mt-1 text-xs text-apple-red">{{ $message }}</p> @enderror
        </x-card>

        {{-- Bateria --}}
        <x-card>
            <h3 class="text-base font-semibold text-apple-text mb-1">Saúde da Bateria</h3>
            <p class="text-xs text-apple-muted mb-4">Modificadores aplicados conforme a faixa de saúde da bateria</p>

            @php
                $batteryRules = $settings->getBatteryRules();
                $batteryLabels = [
                    0 => ['label' => 'Excelente (≥ 90%)', 'field' => 'battery_excellent', 'class' => 'text-green-600'],
                    1 => ['label' => 'Bom (80–89%)', 'field' => 'battery_good', 'class' => 'text-yellow-600'],
                    2 => ['label' => 'Regular (70–79%)', 'field' => 'battery_regular', 'class' => 'text-orange-600'],
                    3 => ['label' => 'Ruim (< 70%)', 'field' => 'battery_bad', 'class' => 'text-red-600'],
                ];
            @endphp

            <div class="space-y-3">
                @foreach($batteryLabels as $i => $meta)
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm {{ $meta['class'] }} font-medium">{{ $meta['label'] }}</span>
                        <div class="flex items-center gap-1">
                            <input type="number" name="{{ $meta['field'] }}" step="0.5" min="-50" max="50"
                                   value="{{ old($meta['field'], $batteryRules[$i]['modifier'] ?? 0) }}"
                                   class="input-field w-20 text-right" required>
                            <span class="text-sm text-apple-muted">%</span>
                        </div>
                    </div>
                    @error($meta['field']) <p class="text-xs text-apple-red">{{ $message }}</p> @enderror
                @endforeach
            </div>
        </x-card>

        {{-- Estado do Aparelho --}}
        <x-card>
            <h3 class="text-base font-semibold text-apple-text mb-1">Estado do Aparelho</h3>
            <p class="text-xs text-apple-muted mb-4">Modificadores conforme o histórico de manutenção</p>

            @php
                $stateOptions = $settings->getDeviceStateOptions();
            @endphp

            <div class="space-y-3">
                <div class="flex items-center justify-between gap-4">
                    <span class="text-sm text-apple-text">Original (nunca aberto)</span>
                    <div class="flex items-center gap-1">
                        <input type="number" name="state_original" step="0.5" min="-50" max="50"
                               value="{{ old('state_original', $stateOptions['original'] ?? 0) }}"
                               class="input-field w-20 text-right" required>
                        <span class="text-sm text-apple-muted">%</span>
                    </div>
                </div>
                @error('state_original') <p class="text-xs text-apple-red">{{ $message }}</p> @enderror

                <div class="flex items-center justify-between gap-4">
                    <span class="text-sm text-apple-text">Reparado (já foi aberto / trocou peça)</span>
                    <div class="flex items-center gap-1">
                        <input type="number" name="state_repaired" step="0.5" min="-50" max="50"
                               value="{{ old('state_repaired', $stateOptions['repaired'] ?? -10) }}"
                               class="input-field w-20 text-right" required>
                        <span class="text-sm text-apple-muted">%</span>
                    </div>
                </div>
                @error('state_repaired') <p class="text-xs text-apple-red">{{ $message }}</p> @enderror
            </div>
        </x-card>

        {{-- Acessórios --}}
        <x-card>
            <h3 class="text-base font-semibold text-apple-text mb-1">Acessórios</h3>
            <p class="text-xs text-apple-muted mb-4">Modificadores conforme a presença de caixa e cabo</p>

            @php
                $accOptions = $settings->getAccessoryOptions();
            @endphp

            <div class="space-y-3">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <span class="text-sm text-green-600 font-medium">Completo</span>
                        <span class="text-xs text-apple-muted ml-1">(possui caixa e cabo)</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <input type="number" name="acc_complete" step="0.5" min="-50" max="50"
                               value="{{ old('acc_complete', $accOptions['complete'] ?? 3) }}"
                               class="input-field w-20 text-right" required>
                        <span class="text-sm text-apple-muted">%</span>
                    </div>
                </div>
                @error('acc_complete') <p class="text-xs text-apple-red">{{ $message }}</p> @enderror

                <div class="flex items-center justify-between gap-4">
                    <div>
                        <span class="text-sm text-yellow-600 font-medium">Parcial</span>
                        <span class="text-xs text-apple-muted ml-1">(possui caixa ou cabo)</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <input type="number" name="acc_partial" step="0.5" min="-50" max="50"
                               value="{{ old('acc_partial', $accOptions['partial'] ?? 0) }}"
                               class="input-field w-20 text-right" required>
                        <span class="text-sm text-apple-muted">%</span>
                    </div>
                </div>
                @error('acc_partial') <p class="text-xs text-apple-red">{{ $message }}</p> @enderror

                <div class="flex items-center justify-between gap-4">
                    <div>
                        <span class="text-sm text-red-600 font-medium">Nenhum</span>
                        <span class="text-xs text-apple-muted ml-1">(sem caixa e sem cabo)</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <input type="number" name="acc_none" step="0.5" min="-50" max="50"
                               value="{{ old('acc_none', $accOptions['none'] ?? -3) }}"
                               class="input-field w-20 text-right" required>
                        <span class="text-sm text-apple-muted">%</span>
                    </div>
                </div>
                @error('acc_none') <p class="text-xs text-apple-red">{{ $message }}</p> @enderror
            </div>
        </x-card>

        <button type="submit" class="btn-primary w-full">Salvar Configurações</button>
    </form>
</x-layouts.app>

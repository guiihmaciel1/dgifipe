<x-layouts.app title="Configurações">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-apple-text tracking-tight">Configurações da Empresa</h1>
        <p class="text-apple-muted mt-1">{{ $company->name }}</p>
    </div>

    <x-card class="max-w-lg">
        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="label">Margem Padrão (%)</label>
                <input type="number" name="default_margin" step="0.5" min="0" max="50"
                       value="{{ old('default_margin', $settings->default_margin ?? 15) }}"
                       class="input-field" required>
                @error('default_margin') <p class="mt-1 text-xs text-apple-red">{{ $message }}</p> @enderror
            </div>

            <div>
                <h3 class="label mb-3">Descontos por Condição (%)</h3>
                @php
                    $discounts = $settings->condition_discounts ?? config('dgifipe.default_condition_discounts');
                    $condLabels = [
                        'no_box' => 'Sem caixa',
                        'no_cable' => 'Sem cabo',
                        'screen_replaced' => 'Tela trocada',
                        'face_id_issue' => 'Face ID com problema',
                    ];
                @endphp

                <div class="space-y-3">
                    @foreach($condLabels as $key => $label)
                        <div class="flex items-center justify-between gap-4">
                            <label class="text-sm text-apple-text">{{ $label }}</label>
                            <input type="number" name="{{ $key }}" step="0.5" min="0" max="30"
                                   value="{{ old($key, $discounts[$key] ?? 0) }}"
                                   class="input-field w-24 text-right">
                        </div>
                        @error($key) <p class="text-xs text-apple-red">{{ $message }}</p> @enderror
                    @endforeach
                </div>
            </div>

            <button type="submit" class="btn-primary w-full">Salvar Configurações</button>
        </form>
    </x-card>
</x-layouts.app>

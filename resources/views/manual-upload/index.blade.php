<x-layouts.app title="Upload Manual">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-apple-text tracking-tight">Upload Manual</h1>
        <p class="text-apple-muted mt-1">Adicione anúncios manualmente ao banco de dados.</p>
    </div>

    <x-card class="max-w-lg">
        <form method="POST" action="{{ route('manual-upload') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="label">Modelo</label>
                <select name="model" class="input-field" required>
                    @foreach(array_keys($models) as $model)
                        <option value="{{ $model }}" @selected(old('model') === $model)>{{ $model }}</option>
                    @endforeach
                </select>
                @error('model') <p class="mt-1 text-xs text-apple-red">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">Armazenamento</label>
                <select name="storage" class="input-field" required>
                    @foreach(['64GB','128GB','256GB','512GB','1TB'] as $s)
                        <option value="{{ $s }}" @selected(old('storage') === $s)>{{ $s }}</option>
                    @endforeach
                </select>
                @error('storage') <p class="mt-1 text-xs text-apple-red">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">Preço (R$)</label>
                <input type="number" name="price" step="0.01" min="100" max="20000"
                       value="{{ old('price') }}" class="input-field" placeholder="2500.00" required>
                @error('price') <p class="mt-1 text-xs text-apple-red">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">Cidade</label>
                <select name="city" class="input-field" required>
                    @foreach($cities as $city)
                        <option value="{{ $city }}" @selected(old('city') === $city)>{{ $city }}</option>
                    @endforeach
                </select>
                @error('city') <p class="mt-1 text-xs text-apple-red">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">Título do Anúncio (opcional)</label>
                <input type="text" name="title" value="{{ old('title') }}" class="input-field"
                       placeholder="iPhone 14 Pro 256GB Preto">
                @error('title') <p class="mt-1 text-xs text-apple-red">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">Screenshot (opcional)</label>
                <input type="file" name="screenshot" accept="image/*"
                       class="block w-full text-sm text-apple-muted file:mr-4 file:py-2 file:px-4
                              file:rounded-apple file:border-0 file:text-sm file:font-medium
                              file:bg-apple-bg file:text-apple-text hover:file:bg-gray-200">
                @error('screenshot') <p class="mt-1 text-xs text-apple-red">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="btn-primary w-full">Cadastrar Anúncio</button>
        </form>
    </x-card>
</x-layouts.app>

<x-layouts.app title="Editar Empresa">
    <div class="mb-6">
        <a href="{{ route('superadmin.companies.index') }}" class="text-sm text-apple-blue hover:underline">&larr; Voltar</a>
        <h1 class="text-2xl font-bold text-apple-text tracking-tight mt-2">Editar: {{ $company->name }}</h1>
    </div>

    <x-card class="max-w-lg">
        <form method="POST" action="{{ route('superadmin.companies.update', $company) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="label">Nome da Empresa</label>
                <input type="text" name="name" value="{{ old('name', $company->name) }}" class="input-field" required>
                @error('name') <p class="mt-1 text-xs text-apple-red">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="btn-primary w-full">Salvar</button>
        </form>
    </x-card>
</x-layouts.app>

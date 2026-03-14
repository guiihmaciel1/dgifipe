<x-layouts.app title="Novo Usuário">
    <div class="mb-6">
        <a href="{{ route('superadmin.companies.show', $company) }}" class="text-sm text-apple-blue hover:underline">&larr; Voltar para {{ $company->name }}</a>
        <h1 class="text-2xl font-bold text-apple-text tracking-tight mt-2">Novo Usuário</h1>
        <p class="text-apple-muted mt-1">Criar usuário para <strong>{{ $company->name }}</strong></p>
    </div>

    <x-card class="max-w-lg">
        <form method="POST" action="{{ route('superadmin.companies.users.store', $company) }}" class="space-y-5">
            @csrf

            <div>
                <label class="label">Nome</label>
                <input type="text" name="name" value="{{ old('name') }}" class="input-field" required>
                @error('name') <p class="mt-1 text-xs text-apple-red">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">E-mail</label>
                <input type="email" name="email" value="{{ old('email') }}" class="input-field" required>
                @error('email') <p class="mt-1 text-xs text-apple-red">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">Função</label>
                <select name="role" class="input-field" required>
                    <option value="seller" @selected(old('role') === 'seller')>Vendedor</option>
                    <option value="admin" @selected(old('role') === 'admin')>Administrador</option>
                </select>
                @error('role') <p class="mt-1 text-xs text-apple-red">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">Senha</label>
                <input type="password" name="password" class="input-field" minlength="8" required>
                @error('password') <p class="mt-1 text-xs text-apple-red">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="btn-primary w-full">Criar Usuário</button>
        </form>
    </x-card>
</x-layouts.app>

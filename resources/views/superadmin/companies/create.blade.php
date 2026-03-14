<x-layouts.app title="Nova Empresa">
    <div class="mb-6">
        <a href="{{ route('superadmin.companies.index') }}" class="text-sm text-apple-blue hover:underline">&larr; Voltar</a>
        <h1 class="text-2xl font-bold text-apple-text tracking-tight mt-2">Nova Empresa</h1>
        <p class="text-apple-muted mt-1">Crie a empresa e seu administrador inicial.</p>
    </div>

    <x-card class="max-w-lg">
        <form method="POST" action="{{ route('superadmin.companies.store') }}" class="space-y-6">
            @csrf

            <div>
                <h3 class="label mb-3">Dados da Empresa</h3>
                <div>
                    <label class="label">Nome da Empresa</label>
                    <input type="text" name="company_name" value="{{ old('company_name') }}" class="input-field" placeholder="Ex: iPhone Store SP" required>
                    @error('company_name') <p class="mt-1 text-xs text-apple-red">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="border-t border-apple-separator pt-6">
                <h3 class="label mb-3">Administrador da Empresa</h3>
                <div class="space-y-4">
                    <div>
                        <label class="label">Nome</label>
                        <input type="text" name="admin_name" value="{{ old('admin_name') }}" class="input-field" required>
                        @error('admin_name') <p class="mt-1 text-xs text-apple-red">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="label">E-mail</label>
                        <input type="email" name="admin_email" value="{{ old('admin_email') }}" class="input-field" required>
                        @error('admin_email') <p class="mt-1 text-xs text-apple-red">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="label">Senha</label>
                        <input type="password" name="admin_password" class="input-field" minlength="8" required>
                        @error('admin_password') <p class="mt-1 text-xs text-apple-red">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-primary w-full">Criar Empresa + Admin</button>
        </form>
    </x-card>
</x-layouts.app>

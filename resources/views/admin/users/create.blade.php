<x-layouts.app title="Novo Membro">
    <div class="mb-6">
        <a href="{{ route('admin.team.index') }}" class="text-sm text-apple-blue hover:underline">&larr; Voltar</a>
        <h1 class="text-2xl font-bold text-apple-text tracking-tight mt-2">Novo Membro</h1>
    </div>

    <x-card class="max-w-lg">
        <form method="POST" action="{{ route('admin.team.store') }}" class="space-y-5">
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
                <input type="password" name="password" class="input-field" required minlength="8">
                @error('password') <p class="mt-1 text-xs text-apple-red">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">Confirmar Senha</label>
                <input type="password" name="password_confirmation" class="input-field" required>
            </div>

            <button type="submit" class="btn-primary w-full">Criar Membro</button>
        </form>
    </x-card>
</x-layouts.app>

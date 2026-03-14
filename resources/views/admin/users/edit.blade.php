<x-layouts.app title="Editar Membro">
    <div class="mb-6">
        <a href="{{ route('admin.team.index') }}" class="text-sm text-apple-blue hover:underline">&larr; Voltar</a>
        <h1 class="text-2xl font-bold text-apple-text tracking-tight mt-2">Editar: {{ $user->name }}</h1>
    </div>

    <x-card class="max-w-lg">
        <form method="POST" action="{{ route('admin.team.update', $user) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="label">Nome</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input-field" required>
                @error('name') <p class="mt-1 text-xs text-apple-red">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">E-mail</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-field" required>
                @error('email') <p class="mt-1 text-xs text-apple-red">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">Função</label>
                <select name="role" class="input-field" required>
                    <option value="seller" @selected($user->role === 'seller')>Vendedor</option>
                    <option value="admin" @selected($user->role === 'admin')>Administrador</option>
                </select>
                @error('role') <p class="mt-1 text-xs text-apple-red">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">Nova Senha (deixe em branco para manter)</label>
                <input type="password" name="password" class="input-field" minlength="8">
                @error('password') <p class="mt-1 text-xs text-apple-red">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">Confirmar Nova Senha</label>
                <input type="password" name="password_confirmation" class="input-field">
            </div>

            <button type="submit" class="btn-primary w-full">Salvar Alterações</button>
        </form>
    </x-card>
</x-layouts.app>

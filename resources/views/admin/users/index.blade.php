<x-layouts.app title="Equipe">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-apple-text tracking-tight">Equipe</h1>
            <p class="text-apple-muted mt-1">Gerencie os membros da sua empresa.</p>
        </div>
        <a href="{{ route('admin.team.create') }}" class="btn-primary text-sm">Novo Membro</a>
    </div>

    <div class="space-y-3">
        @foreach($users as $user)
            <x-card>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-apple-bg flex items-center justify-center">
                            <span class="text-sm font-medium text-apple-muted">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-semibold text-apple-text">{{ $user->name }}</p>
                            <p class="text-sm text-apple-muted">{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="badge-{{ $user->role === 'admin' ? 'success' : 'warning' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                        <span class="badge-{{ $user->is_active ? 'success' : 'danger' }}">
                            {{ $user->is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.team.edit', $user) }}"
                               class="p-2 rounded-apple text-apple-muted hover:bg-apple-bg transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                                </svg>
                            </a>
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.team.toggle', $user) }}">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit"
                                            class="p-2 rounded-apple text-apple-muted hover:bg-apple-bg transition-colors"
                                            title="{{ $user->is_active ? 'Desativar' : 'Ativar' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $user->is_active ? 'M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z' }}"/>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </x-card>
        @endforeach
    </div>
</x-layouts.app>

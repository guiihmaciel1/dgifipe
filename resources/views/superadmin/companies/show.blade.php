<x-layouts.app title="{{ $company->name }}">
    <div class="mb-6">
        <a href="{{ route('superadmin.companies.index') }}" class="text-sm text-apple-blue hover:underline">&larr; Todas as empresas</a>
        <div class="flex items-center justify-between mt-2">
            <div>
                <h1 class="text-2xl font-bold text-apple-text tracking-tight">{{ $company->name }}</h1>
                <p class="text-apple-muted mt-1">
                    <span class="{{ $company->active ? 'badge-success' : 'badge-danger' }}">{{ $company->active ? 'Ativa' : 'Inativa' }}</span>
                    · Criada em {{ $company->created_at->format('d/m/Y') }}
                </p>
            </div>
            <a href="{{ route('superadmin.companies.users.create', $company) }}" class="btn-primary text-sm">Novo Usuário</a>
        </div>
    </div>

    @if($company->settings)
        <x-card class="mb-6">
            <h2 class="label mb-3">Configurações</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-apple-muted">Margem</p>
                    <p class="font-semibold text-apple-text">{{ $company->settings->default_margin }}%</p>
                </div>
                @if($company->settings->condition_discounts)
                    @foreach($company->settings->condition_discounts as $key => $val)
                        <div>
                            <p class="text-apple-muted">{{ str_replace('_', ' ', ucfirst($key)) }}</p>
                            <p class="font-semibold text-apple-text">-{{ $val }}%</p>
                        </div>
                    @endforeach
                @endif
            </div>
        </x-card>
    @endif

    <h2 class="text-lg font-semibold text-apple-text mb-3">Usuários ({{ $company->users->count() }})</h2>

    @if($company->users->isEmpty())
        <x-card>
            <p class="text-center text-apple-muted py-8">Nenhum usuário cadastrado.</p>
        </x-card>
    @else
        <div class="space-y-3">
            @foreach($company->users as $user)
                <x-card>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-apple-bg flex items-center justify-center">
                                <span class="text-sm font-medium text-apple-muted">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-apple-text">{{ $user->name }}</p>
                                <p class="text-sm text-apple-muted">{{ $user->email }}</p>
                                @if($user->last_login_at)
                                    <p class="text-xs text-apple-muted">Último login: {{ $user->last_login_at->format('d/m/Y H:i') }}</p>
                                @else
                                    <p class="text-xs text-apple-muted">Nunca acessou</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="{{ $user->role === 'admin' ? 'badge-success' : 'badge-warning' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                            <span class="{{ $user->is_active ? 'badge-success' : 'badge-danger' }}">
                                {{ $user->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                            <form method="POST" action="{{ route('superadmin.users.toggle', $user) }}">
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
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif
</x-layouts.app>

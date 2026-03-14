<x-layouts.app title="Gestão de Empresas">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-apple-text tracking-tight">Empresas (Tenants)</h1>
            <p class="text-apple-muted mt-1">Gerencie todas as empresas do sistema.</p>
        </div>
        <a href="{{ route('superadmin.companies.create') }}" class="btn-primary text-sm">Nova Empresa</a>
    </div>

    @if($companies->isEmpty())
        <x-card>
            <p class="text-center text-apple-muted py-12">Nenhuma empresa cadastrada.</p>
        </x-card>
    @else
        <div class="space-y-3">
            @foreach($companies as $company)
                <x-card class="hover:shadow-apple-md transition-shadow duration-200">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('superadmin.companies.show', $company) }}" class="flex items-center gap-4 flex-1 min-w-0">
                            <div class="w-10 h-10 rounded-apple bg-apple-blue/10 flex items-center justify-center shrink-0">
                                <span class="text-sm font-bold text-apple-blue">{{ substr($company->name, 0, 2) }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-apple-text truncate">{{ $company->name }}</p>
                                <p class="text-sm text-apple-muted">{{ $company->users_count }} usuário(s) · Criada em {{ $company->created_at->format('d/m/Y') }}</p>
                            </div>
                        </a>
                        <div class="flex items-center gap-3 ml-4">
                            <span class="{{ $company->active ? 'badge-success' : 'badge-danger' }}">
                                {{ $company->active ? 'Ativa' : 'Inativa' }}
                            </span>
                            <a href="{{ route('superadmin.companies.edit', $company) }}"
                               class="p-2 rounded-apple text-apple-muted hover:bg-apple-bg transition-colors" title="Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('superadmin.companies.toggle', $company) }}">
                                @csrf
                                @method('PUT')
                                <button type="submit"
                                        class="p-2 rounded-apple text-apple-muted hover:bg-apple-bg transition-colors"
                                        title="{{ $company->active ? 'Desativar' : 'Ativar' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $company->active ? 'M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z' }}"/>
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

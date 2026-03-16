<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DG iFIPE</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-apple-bg" x-data="{ sidebarOpen: false }">

    {{-- Desktop Sidebar --}}
    <aside class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-60 lg:flex-col">
        <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white border-r border-apple-separator px-6 py-8">
            <div class="flex items-center gap-3 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-apple-text" viewBox="0 0 814 1000" fill="currentColor">
                    <path d="M788.1 340.9c-5.8 4.5-108.2 62.2-108.2 190.5 0 148.4 130.3 200.9 134.2 202.2-.6 3.2-20.7 71.9-68.7 141.9-42.8 61.6-87.5 123.1-155.5 123.1s-85.5-39.5-164-39.5c-76.5 0-103.7 40.8-165.9 40.8s-105.6-57.8-155.5-127.4c-58.3-81.3-105.9-207.9-105.9-328.5 0-193.1 125.4-295.7 248.8-295.7 65.6 0 120.3 43.1 161.4 43.1 39.1 0 100.1-45.7 174.5-45.7 28.2 0 129.5 2.6 196.8 99.2zM554.1 159.4c31.1-36.9 53.1-88.1 53.1-139.3 0-7.1-.6-14.3-1.9-20.1-50.6 1.9-110.8 33.7-147.1 75.8-28.5 32.4-55.1 83.6-55.1 135.5 0 7.8 1.3 15.6 1.9 18.1 3.2.6 8.4 1.3 13.6 1.3 45.4 0 103.5-30.4 135.5-71.3z"/>
                </svg>
                <span class="text-lg font-semibold text-apple-text tracking-tight">DG iFIPE</span>
            </div>

            <nav class="flex flex-col gap-1">
                @if(auth()->user()->isSuperAdmin())
                    <x-sidebar-link route="superadmin.companies.index" icon="building">Empresas</x-sidebar-link>
                @else
                    <x-sidebar-link route="dashboard" icon="home">Dashboard</x-sidebar-link>
                    <x-sidebar-link route="evaluator" icon="calculator">Avaliador</x-sidebar-link>
                    <x-sidebar-link route="market-radar" icon="radar">Market Radar</x-sidebar-link>
                    <x-sidebar-link route="alerts.index" icon="bell">Oportunidades</x-sidebar-link>
                    <x-sidebar-link route="history" icon="clock">Histórico</x-sidebar-link>

                    @if(auth()->user()->isAdmin())
                        <div class="mt-4 mb-2">
                            <span class="label text-xs">Administração</span>
                        </div>
                        <x-sidebar-link route="admin.team.index" icon="users">Equipe</x-sidebar-link>
                        <x-sidebar-link route="admin.settings" icon="settings">Configurações</x-sidebar-link>
                    @endif
                @endif
            </nav>

            <div class="mt-auto pt-4 border-t border-apple-separator">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-full bg-apple-bg flex items-center justify-center">
                        <span class="text-sm font-medium text-apple-muted">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-apple-text truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-apple-muted truncate">{{ auth()->user()->company?->name ?? 'Super Admin' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-apple-red hover:underline">Sair</button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Mobile Header --}}
    <header class="lg:hidden sticky top-0 z-40 bg-white/80 backdrop-blur-xl border-b border-apple-separator">
        <div class="flex items-center justify-between px-4 py-3">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-apple-text" viewBox="0 0 814 1000" fill="currentColor">
                    <path d="M788.1 340.9c-5.8 4.5-108.2 62.2-108.2 190.5 0 148.4 130.3 200.9 134.2 202.2-.6 3.2-20.7 71.9-68.7 141.9-42.8 61.6-87.5 123.1-155.5 123.1s-85.5-39.5-164-39.5c-76.5 0-103.7 40.8-165.9 40.8s-105.6-57.8-155.5-127.4c-58.3-81.3-105.9-207.9-105.9-328.5 0-193.1 125.4-295.7 248.8-295.7 65.6 0 120.3 43.1 161.4 43.1 39.1 0 100.1-45.7 174.5-45.7 28.2 0 129.5 2.6 196.8 99.2zM554.1 159.4c31.1-36.9 53.1-88.1 53.1-139.3 0-7.1-.6-14.3-1.9-20.1-50.6 1.9-110.8 33.7-147.1 75.8-28.5 32.4-55.1 83.6-55.1 135.5 0 7.8 1.3 15.6 1.9 18.1 3.2.6 8.4 1.3 13.6 1.3 45.4 0 103.5-30.4 135.5-71.3z"/>
                </svg>
                <span class="font-semibold text-apple-text">DG iFIPE</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm text-apple-muted">{{ auth()->user()->company?->name ?? 'Super Admin' }}</span>
                @unless(auth()->user()->isSuperAdmin())
                    <a href="{{ route('alerts.index') }}" class="relative p-1.5 rounded-apple text-apple-muted hover:text-apple-blue hover:bg-apple-blue/10 transition-colors" title="Oportunidades"
                       x-data="alertBell()" x-init="fetchCount()">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                        <span x-show="count > 0" x-text="count > 9 ? '9+' : count"
                              class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] flex items-center justify-center text-[10px] font-bold text-white bg-apple-red rounded-full px-1"
                              x-transition></span>
                    </a>
                @endunless
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-1.5 rounded-apple text-apple-muted hover:text-apple-red hover:bg-apple-red/10 transition-colors" title="Sair">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="lg:pl-60">
        <div class="px-4 py-6 lg:px-8 lg:py-8 max-w-7xl mx-auto">
            @if(session('success'))
                <div class="mb-6 p-4 rounded-apple-lg bg-apple-green/10 text-apple-green text-sm font-medium"
                     x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     x-transition>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-apple-lg bg-apple-red/10 text-apple-red text-sm font-medium"
                     x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     x-transition>
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>

    {{-- Mobile Bottom Navigation --}}
    <nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white/80 backdrop-blur-xl border-t border-apple-separator safe-area-bottom">
        <div class="flex items-center justify-around py-2">
            @if(auth()->user()->isSuperAdmin())
                <x-mobile-tab route="superadmin.companies.index" icon="building" label="Empresas" />
            @else
                <x-mobile-tab route="dashboard" icon="home" label="Home" />
                <x-mobile-tab route="evaluator" icon="calculator" label="Avaliar" />
                <x-mobile-tab route="market-radar" icon="radar" label="Radar" />
                <x-mobile-tab route="history" icon="clock" label="Histórico" />
                @if(auth()->user()->isAdmin())
                    <x-mobile-tab route="admin.settings" icon="settings" label="Config" />
                @endif
            @endif
        </div>
    </nav>

    {{-- Bottom padding for mobile nav --}}
    <div class="lg:hidden h-20"></div>

    <script>
        function alertBell() {
            return {
                count: 0,
                fetchCount() {
                    fetch('{{ route("alerts.count") }}', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(r => r.json())
                    .then(data => this.count = data.count)
                    .catch(() => {});
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>

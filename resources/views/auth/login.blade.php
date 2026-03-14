<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>DG iFIPE</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-apple-bg flex items-center justify-center px-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <div class="mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mx-auto text-apple-text" viewBox="0 0 814 1000" fill="currentColor">
                    <path d="M788.1 340.9c-5.8 4.5-108.2 62.2-108.2 190.5 0 148.4 130.3 200.9 134.2 202.2-.6 3.2-20.7 71.9-68.7 141.9-42.8 61.6-87.5 123.1-155.5 123.1s-85.5-39.5-164-39.5c-76.5 0-103.7 40.8-165.9 40.8s-105.6-57.8-155.5-127.4c-58.3-81.3-105.9-207.9-105.9-328.5 0-193.1 125.4-295.7 248.8-295.7 65.6 0 120.3 43.1 161.4 43.1 39.1 0 100.1-45.7 174.5-45.7 28.2 0 129.5 2.6 196.8 99.2zM554.1 159.4c31.1-36.9 53.1-88.1 53.1-139.3 0-7.1-.6-14.3-1.9-20.1-50.6 1.9-110.8 33.7-147.1 75.8-28.5 32.4-55.1 83.6-55.1 135.5 0 7.8 1.3 15.6 1.9 18.1 3.2.6 8.4 1.3 13.6 1.3 45.4 0 103.5-30.4 135.5-71.3z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-apple-text tracking-tight">DG iFIPE</h1>
            <p class="text-apple-muted mt-1">A tabela FIPE dos iPhones</p>
        </div>

        @if(session('error'))
            <div class="mb-4 p-4 rounded-apple-lg bg-apple-red/10 text-apple-red text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        <div class="card">
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="label">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="input-field" placeholder="seu@email.com" required autofocus>
                    @error('email')
                        <p class="mt-1 text-xs text-apple-red">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="label">Senha</label>
                    <input type="password" id="password" name="password"
                           class="input-field" placeholder="••••••••" required>
                    @error('password')
                        <p class="mt-1 text-xs text-apple-red">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-primary w-full">Entrar</button>
            </form>
        </div>

        @if(app()->environment('local'))
            <div class="mt-6" x-data>
                <p class="text-center text-xs text-apple-muted mb-3">Acesso rápido (dev)</p>
                <div class="flex gap-2">
                    <button type="button"
                            @click="document.getElementById('email').value='admin@dgifipe.com'; document.getElementById('password').value='admin123'"
                            class="flex-1 px-3 py-2 text-xs font-medium rounded-apple bg-purple-50 text-purple-700 hover:bg-purple-100 transition-colors">
                        Super Admin
                    </button>
                    <button type="button"
                            @click="document.getElementById('email').value='admin@dgstore.com'; document.getElementById('password').value='password'"
                            class="flex-1 px-3 py-2 text-xs font-medium rounded-apple bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors">
                        Admin
                    </button>
                    <button type="button"
                            @click="document.getElementById('email').value='seller@dgstore.com'; document.getElementById('password').value='password'"
                            class="flex-1 px-3 py-2 text-xs font-medium rounded-apple bg-green-50 text-green-700 hover:bg-green-100 transition-colors">
                        Vendedor
                    </button>
                </div>
            </div>
        @endif

        <p class="text-center text-xs text-apple-muted mt-6">
            &copy; {{ date('Y') }} DG iFIPE. Todos os direitos reservados.
        </p>
    </div>
</body>
</html>

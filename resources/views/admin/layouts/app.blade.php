<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title') - HelpDesk Pro</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS (via CDN for guaranteed styling out of the box) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#4F46E5', // Indigo 600
                        secondary: '#8B5CF6', // Violet 500
                        dark: '#0F172A', // Slate 900
                        surface: '#1E293B', // Slate 800
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0F172A; color: #F8FAFC; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .sidebar-link { transition: all 0.3s ease; }
        .sidebar-link:hover { background-color: rgba(79, 70, 229, 0.2); transform: translateX(5px); }
        .fade-in { animation: fadeIn 0.5s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>

<body class="flex h-screen overflow-hidden antialiased selection:bg-primary selection:text-white">
    <!-- Sidebar / Navigation -->
    <aside class="w-64 glass shadow-2xl flex flex-col justify-between hidden md:flex border-r border-slate-700/50">
        <div>
            <div class="h-20 flex items-center justify-center border-b border-slate-700/50">
                <h1 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary to-secondary">
                    <i class="fa-solid fa-headset mr-2"></i>HelpDesk<span class="font-light">Pro</span>
                </h1>
            </div>
            
            <nav class="p-4 space-y-2 mt-4">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4 pl-3">Menu Principal</p>
                <a href="{{ route('posts.index') }}" class="sidebar-link flex items-center p-3 rounded-lg text-slate-300 hover:text-white">
                    <i class="fas fa-ticket-alt w-6 text-center text-primary"></i>
                    <span class="font-medium ml-3">Todos os Chamados</span>
                </a>
                <a href="{{ route('posts.create') }}" class="sidebar-link flex items-center p-3 rounded-lg text-slate-300 hover:text-white">
                    <i class="fas fa-plus-circle w-6 text-center text-secondary"></i>
                    <span class="font-medium ml-3">Novo Chamado</span>
                </a>
            </nav>
        </div>
        
        <div class="p-4 border-t border-slate-700/50">
            <div class="flex items-center mb-4 px-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-bold shadow-lg">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
                <div class="ml-3">
                    <p class="text-sm font-semibold text-white">{{ Auth::user()->name ?? 'Agente' }}</p>
                    <p class="text-xs text-slate-400">Suporte Técnico</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full sidebar-link flex items-center justify-center p-2 rounded-lg text-red-400 hover:text-red-300 hover:bg-red-400/10">
                    <i class="fas fa-sign-out-alt mr-2"></i> Sair do Sistema
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-y-auto bg-[#0B1120]">
        <!-- Topbar Mobile -->
        <header class="md:hidden h-16 glass flex items-center justify-between px-4 sticky top-0 z-50">
            <h1 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary to-secondary">HelpDesk Pro</h1>
            <button class="text-slate-300 hover:text-white focus:outline-none"><i class="fas fa-bars text-xl"></i></button>
        </header>

        <!-- Content Area -->
        <div class="p-6 md:p-10 max-w-7xl mx-auto w-full fade-in">
            @yield('content')
        </div>
    </main>
</body>
</html>
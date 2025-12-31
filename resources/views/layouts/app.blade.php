<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Healthcare Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full antialiased text-slate-800">

    <nav class="bg-white sticky top-0 z-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <div class="flex items-center">
                    <a href="/" class="flex-shrink-0">
                        <span class="text-slate-900 text-xl font-bold tracking-tight">Healthcare</span>
                    </a>
                </div>

                <div class="flex space-x-8 h-full">
                    
                    <a href="/" 
                       class="inline-flex items-center px-1 pt-1 text-sm font-medium transition-colors duration-200 border-b-2 h-full
                       {{ request()->is('/') 
                            ? 'border-blue-600 text-blue-600'  
                            : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                        Dashboard
                    </a>

                    <a href="/patients" 
                       class="inline-flex items-center px-1 pt-1 text-sm font-medium transition-colors duration-200 border-b-2 h-full
                       {{ request()->is('patients*') 
                            ? 'border-blue-600 text-blue-600' 
                            : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                        Data Pasien
                    </a>

                </div>

            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

</body>
</html>
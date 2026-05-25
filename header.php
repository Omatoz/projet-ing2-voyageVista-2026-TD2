<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VoyageVista — Plateforme de planification</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 flex flex-col min-h-screen antialiased">

    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            
            <a href="index.php" class="flex items-center space-x-2 tracking-tighter">
                <span class="text-xl font-black text-slate-900 uppercase">Voyage</span>
                <span class="text-xl font-light text-indigo-600 uppercase tracking-widest">Vista</span>
            </a>

            <nav class="hidden md:flex space-x-8 text-xs font-bold uppercase tracking-wider text-gray-600">
                <a href="index.php" class="text-indigo-600 border-b-2 border-indigo-600 pb-1">Vols</a>
                <a href="#" class="hover:text-indigo-600 transition pb-1 border-b-2 border-transparent">Hôtels</a>
                <a href="#" class="hover:text-indigo-600 transition pb-1 border-b-2 border-transparent">Séjours</a>
                <a href="#" class="hover:text-indigo-600 transition pb-1 border-b-2 border-transparent">Activités</a>
                <a href="#" class="hover:text-indigo-600 transition pb-1 border-b-2 border-transparent">Voitures</a>
            </nav>

            <a href="auth.php" class="bg-slate-900 text-white text-xs font-bold uppercase tracking-wider px-5 py-3 rounded-none hover:bg-indigo-600 transition shadow-sm">
                Mon Espace
            </a>
        </div>
    </header>

    <main class="flex-grow">
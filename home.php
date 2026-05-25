<?php include 'header.php'; ?>

<section class="bg-white border-b border-gray-200 py-12">
    <div class="max-w-7xl mx-auto px-6">
        <div class="mb-8">
            <h1 class="text-2xl font-black uppercase tracking-tight text-slate-900">Planifiez. Explorez. Vivez.</h1>
            <p class="text-xs uppercase tracking-widest text-gray-400 mt-1">Agence de voyages — Configuration d'itinéraires</p>
        </div>

        <form action="home.php" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-50 p-4 border border-gray-200">
            <div class="bg-white p-3 border border-gray-200">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Destination</label>
                <input type="text" name="destination" placeholder="Où allez-vous ?" class="w-full text-sm font-semibold bg-transparent outline-none text-slate-800" />
            </div>
            <div class="bg-white p-3 border border-gray-200">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Transport</label>
                <select name="transport" class="w-full text-sm font-semibold bg-transparent outline-none text-slate-700">
                    <option value="avion">Avion</option>
                    <option value="train">Train</option>
                    <option value="bus">Bus</option>
                    <option value="voiture">Voiture</option>
                </select>
            </div>
            <div class="bg-white p-3 border border-gray-200">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Dates & Voyageurs</label>
                <input type="text" placeholder="15 Juin 2026 — 2 adultes" class="w-full text-sm font-semibold bg-transparent outline-none text-slate-600" />
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white font-bold text-xs uppercase tracking-widest hover:bg-indigo-700 transition h-full py-4 md:py-0">
                Rechercher
            </button>
        </form>
    </div>
</section>

<section class="bg-white border-b border-gray-200 py-6">
    <div class="max-w-7xl mx-auto px-6 flex flex-wrap gap-6 items-center">
        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Catégories :</span>
        
        <div class="flex items-center space-x-3">
            <button class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-bold shadow-md shadow-indigo-100">
                PL
            </button>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-900">Plages</span>
        </div>

        <div class="flex items-center space-x-3 group cursor-pointer">
            <button class="w-10 h-10 rounded-full bg-gray-100 text-gray-600 group-hover:bg-indigo-600 group-hover:text-white flex items-center justify-center text-xs font-bold transition">
                MO
            </button>
            <span class="text-xs font-bold uppercase tracking-wider text-gray-500 group-hover:text-slate-900 transition">Montagnes</span>
        </div>

        <div class="flex items-center space-x-3 group cursor-pointer">
            <button class="w-10 h-10 rounded-full bg-gray-100 text-gray-600 group-hover:bg-indigo-600 group-hover:text-white flex items-center justify-center text-xs font-bold transition">
                AV
            </button>
            <span class="text-xs font-bold uppercase tracking-wider text-gray-500 group-hover:text-slate-900 transition">Aventures</span>
        </div>

        <div class="flex items-center space-x-3 group cursor-pointer">
            <button class="w-10 h-10 rounded-full bg-gray-100 text-gray-600 group-hover:bg-indigo-600 group-hover:text-white flex items-center justify-center text-xs font-bold transition">
                DE
            </button>
            <span class="text-xs font-bold uppercase tracking-wider text-gray-500 group-hover:text-slate-900 transition">Détente</span>
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-6 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-6">
            <div class="border-b border-gray-200 pb-3">
                <h2 class="text-xs font-black uppercase tracking-widest text-slate-900">Destinations disponibles</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="bg-white border border-gray-200 p-4">
                    <div class="w-full h-44 bg-emerald-500 mb-4 border border-gray-100 relative">
                        <span class="absolute top-3 right-3 bg-white text-slate-900 text-[10px] font-black uppercase tracking-wider px-2 py-1 border border-gray-200">
                            789 €
                        </span>
                    </div>
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-sm font-bold uppercase text-slate-900 tracking-tight">Bali, Indonésie</h3>
                        <span class="text-[10px] font-bold text-amber-500 uppercase tracking-widest">Avis : Excellent</span>
                    </div>
                    <p class="text-xs text-gray-500 font-light leading-relaxed mb-4">Vol régulier inclus au départ de Paris, hébergement en bord de mer.</p>
                    <button class="w-full bg-gray-900 text-white text-[10px] font-bold uppercase tracking-wider py-2.5 hover:bg-indigo-600 transition">
                        Sélectionner cette destination
                    </button>
                </div>

                <div class="bg-white border border-gray-200 p-4">
                    <div class="w-full h-44 bg-sky-500 mb-4 border border-gray-100 relative">
                        <span class="absolute top-3 right-3 bg-white text-slate-900 text-[10px] font-black uppercase tracking-wider px-2 py-1 border border-gray-200">
                            159 €
                        </span>
                    </div>
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-sm font-bold uppercase text-slate-900 tracking-tight">Ligne de Bus — Europe</h3>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Disponibilité : Haute</span>
                    </div>
                    <p class="text-xs text-gray-500 font-light leading-relaxed mb-4">Brique de transport terrestre reliant les grandes capitales européennes.</p>
                    <button class="w-full bg-gray-900 text-white text-[10px] font-bold uppercase tracking-wider py-2.5 hover:bg-indigo-600 transition">
                        Sélectionner ce transport
                    </button>
                </div>

            </div>
        </div>

        <div class="space-y-6">
            <div class="border-b border-gray-200 pb-3">
                <h2 class="text-xs font-black uppercase tracking-widest text-slate-900">Mon itinéraire composé</h2>
            </div>

            <div class="bg-white border-2 border-slate-900 p-6 space-y-4">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Aperçu du séjour</p>
                
                <div class="space-y-3">
                    <div class="p-3 bg-gray-50 border border-gray-200 flex justify-between items-center">
                        <div>
                            <p class="text-xs font-bold uppercase text-slate-900">Hôtel Tropical Resort</p>
                            <p class="text-[10px] text-gray-400 font-medium">Hébergement — 4 nuits</p>
                        </div>
                        <span class="text-xs font-bold text-slate-900">65 € / nuit</span>
                    </div>

                    <div class="p-3 bg-gray-50 border border-gray-200 flex justify-between items-center">
                        <div>
                            <p class="text-xs font-bold uppercase text-slate-900">Aventure en Suisse</p>
                            <p class="text-[10px] text-gray-400 font-medium">Activité — Excursion</p>
                        </div>
                        <span class="text-xs font-bold text-slate-900">1249 €</span>
                    </div>
                </div>

                <div class="pt-4 border-t border-dashed border-gray-300 flex justify-between items-center">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-900">Prix total estimé</span>
                    <span class="text-lg font-black text-indigo-600">1509 €</span>
                </div>

                <button class="w-full bg-slate-900 text-white text-xs font-bold uppercase tracking-widest py-3.5 hover:bg-indigo-600 transition shadow-sm">
                    Voir tout mon itinéraire
                </button>
            </div>
        </div>

    </div>
</section>

<?php include 'footer.php'; ?>
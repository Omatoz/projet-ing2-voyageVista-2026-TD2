<?php include 'header.php'; ?>

<div class="min-h-[80vh] flex items-center justify-center px-6 py-12 bg-gray-50">
    <div class="max-w-md w-full bg-white border border-gray-200 p-8 space-y-6">
        
        <div class="text-center space-y-1">
            <h2 class="text-xl font-black uppercase tracking-tight text-slate-900">Connexion VoyageVista</h2>
            <p class="text-xs font-light text-gray-400 uppercase tracking-wider">
                Veuillez entrer vos identifiants d'accès
            </p>
        </div>

        <form action="" method="POST" class="space-y-4">
            <div class="bg-gray-50 p-3 border border-gray-200">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Identifiant / Email</label>
                <input type="email" name="email" required class="w-full text-sm font-semibold bg-transparent outline-none text-slate-800" placeholder="nom@domaine.com" />
            </div>

            <div class="bg-gray-50 p-3 border border-gray-200">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Mot de passe</label>
                <input type="password" name="password" required class="w-full text-sm font-semibold bg-transparent outline-none text-slate-800" placeholder="••••••••" />
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white text-xs font-bold uppercase tracking-widest py-3.5 hover:bg-indigo-700 transition shadow-sm">
                Valider la connexion
            </button>
        </form>

        <div class="text-center pt-4 border-t border-gray-100">
            <p class="text-[11px] text-gray-400 font-medium uppercase tracking-wider">
                Pas encore enregistré ? <a href="#" class="text-indigo-600 font-bold hover:underline">Créer un compte client</a>
            </p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
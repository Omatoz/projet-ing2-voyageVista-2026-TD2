<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'header.php'; 
include 'database.php';

$destinations = [];
$sejours = [];
$notifications = [];

if ($conn !== null) {
    try {
        $stmt = $conn->query("SELECT id, titre, description, prix, categorie, couleur_css, image_url FROM destinations ORDER BY id ASC");
        $destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmtPack = $conn->query("SELECT id, titre, description, prix, categorie, image_url FROM sejours ORDER BY id ASC");
        $sejours = $stmtPack->fetchAll(PDO::FETCH_ASSOC);
        if (isset($_SESSION['user_id'])) {
            $stmtNotifs = $conn->prepare("SELECT * FROM notifications WHERE id_utilisateur = ? AND lu = 0 ORDER BY date_creation DESC");
            $stmtNotifs->execute([$_SESSION['user_id']]);
            $notifications = $stmtNotifs->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch(PDOException $e) { echo "Erreur : " . $e->getMessage(); }
}
?>

<link rel="stylesheet" href="index.css">

<style>
    .toggle-container { display: flex; justify-content: center; gap: 10px; margin-bottom: 2rem; background: #f3f4f6; padding: 5px; border-radius: 30px; width: fit-content; margin-left: auto; margin-right: auto; }
    .btn-toggle { padding: 10px 25px; border-radius: 25px; font-weight: 700; font-size: 0.9rem; border: none; cursor: pointer; transition: 0.3s; background: transparent; color: #6b7280; }
    .btn-toggle.active { background: #4f46e5; color: white; box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2); }
    .package-details-box { font-size: 0.75rem; color: #4b5563; background: #f9fafb; padding: 10px; border-radius: 6px; border: 1px solid #e5e7eb; margin-top: 10px; width: 100%; }
    .package-details-box ul { margin: 5px 0 0 15px; padding: 0; }
</style>

<section class="search-section">
    <div class="search-container">
        <div class="title-bloc"><h1>Planifiez. Explorez. Vivez.</h1></div>
        <form action="index.php" method="GET" class="search-form">
            <div class="champ-saisie-bloc">
                <label>Destination</label>
                <input type="text" id="search-destination" placeholder="Où allez-vous ?" oninput="afficherCatalogue()" />
            </div>
            <div class="champ-saisie-bloc">
                <label>Voyageurs</label>
                <input type="number" id="search-voyageurs" min="1" max="20" value="1" onchange="mettreAJourPanier(); afficherCatalogue();" />
            </div>
            <div class="champ-saisie-bloc" style="border:none;">
                <button type="button" id="btn-filtrer-recherche" class="btn-submit-recherche">Rechercher</button>
            </div>
        </form>
    </div>
</section>

<section class="filter-section">
    <div class="toggle-container">
        <button id="btn-alacarte" class="btn-toggle active">À la carte</button>
        <button id="btn-packages" class="btn-toggle">Packages Complets</button>
    </div>

    <div class="filter-container">
        <span class="filter-label">Catégories :</span>
        <div class="filter-item"><button class="btn-categorie-rond actif" data-categorie="tous">TO</button><span class="filter-text">Tous</span></div>
        <div class="filter-item"><button class="btn-categorie-rond inactif" data-categorie="plages">PL</button><span class="filter-text">Plages</span></div>
        <div class="filter-item"><button class="btn-categorie-rond inactif" data-categorie="montagnes">MO</button><span class="filter-text">Montagnes</span></div>
        <div class="filter-item"><button class="btn-categorie-rond inactif" data-categorie="urbain">UR</button><span class="filter-text">Urbain</span></div>
        <div class="filter-item"><button class="btn-categorie-rond inactif" data-categorie="atypiques">AT</button><span class="filter-text">Atypiques</span></div>
        <div class="filter-item"><button class="btn-categorie-rond inactif" data-categorie="aventures">AV</button><span class="filter-text">Aventures</span></div>
        <div class="filter-item"><button class="btn-categorie-rond inactif" data-categorie="detente">DE</button><span class="filter-text">Détente</span></div>
        <div class="filter-item"><button class="btn-categorie-rond inactif" data-categorie="culture">CU</button><span class="filter-text">Culture</span></div>
        <div class="filter-item"><button class="btn-categorie-rond inactif" data-categorie="gastronomie">GA</button><span class="filter-text">Gastronomie</span> </div>
    </div>
</section>

<section class="main-content-section">
    <div class="main-grid">
        <div class="col-catalogue">
            <div class="bloc-title"><h2 id="titre-catalogue">Destinations</h2></div>
            <div class="cards-grid" id="catalogue-voyages"></div>
        </div>
        <div>
            <div class="bloc-title"><h2>Mon itinéraire</h2></div>
            <div class="panier-container">
                <p class="panier-sub" id="panier-statut">0 sélection</p>
                <div class="panier-items-list" id="panier-contenu"></div>
                <div class="panier-total-row">
                    <span class="total-label">Prix total estimé</span>
                    <span class="total-price" id="panier-total">0 €</span>
                </div>
                <button class="btn-panier-main" id="btn-valider-panier" disabled>Continuer vers les transports ➔</button>
            </div>
        </div>
    </div>
</section>

<script>
    const voyagesData = <?php echo json_encode($destinations); ?>;
    const packagesData = <?php echo json_encode($sejours); ?>;
    let panier = [];
    let categorieActuelle = "tous";
    let modeAffichage = "alacarte";

    const catalogueContainer = document.getElementById('catalogue-voyages');
    const panierContenu = document.getElementById('panier-contenu');
    const panierTotal = document.getElementById('panier-total');
    const panierStatut = document.getElementById('panier-statut');
    const btnValiderPanier = document.getElementById('btn-valider-panier');

    document.getElementById('btn-alacarte').addEventListener('click', function() {
        modeAffichage = "alacarte";
        this.classList.add('active');
        document.getElementById('btn-packages').classList.remove('active');
        document.getElementById('titre-catalogue').innerText = "Destinations";
        panier = []; mettreAJourPanier(); // On vide le panier au changement
        afficherCatalogue();
    });

    document.getElementById('btn-packages').addEventListener('click', function() {
        modeAffichage = "packages";
        this.classList.add('active');
        document.getElementById('btn-alacarte').classList.remove('active');
        document.getElementById('titre-catalogue').innerText = "Packages Séjours";
        panier = []; mettreAJourPanier(); // On vide le panier au changement
        afficherCatalogue();
    });

    function afficherCatalogue() {
        catalogueContainer.innerHTML = ""; 
        const texteRecherche = document.getElementById('search-destination').value.toLowerCase();

        const voyagesFiltrés = voyagesData.filter(v => {
            const matchCategorie = (categorieActuelle === "tous" || (v.categorie && v.categorie.split(',').includes(categorieActuelle)));
            const matchTexte = v.titre.toLowerCase().includes(texteRecherche);
            

            if (modeAffichage === "packages") return false;

            return matchCategorie && matchTexte;
        });

        if (modeAffichage === "packages") {
            packagesData.forEach(pack => {
                const texteRecherche = document.getElementById('search-destination').value.toLowerCase();
                if (pack.titre.toLowerCase().includes(texteRecherche)) {
                    const estDansLePanier = panier.some(item => item.id === pack.id && item.isPackage);

                    const cardHtml = `
        <div class="bloc-card">
            <div class="placeholder-image-bloc" style="background-image: url('${pack.image_url}'); background-size: cover; background-position: center;">
                <span class="price-badge">${Math.round(pack.prix)} €</span>
            </div>
            <div class="card-header"><h3 class="card-title">${pack.titre}</h3></div>
            <p class="card-description">${pack.description}</p>
            <button class="btn-action-bloc" onclick="ajouterPackageLocal(${pack.id})" 
                    ${estDansLePanier ? 'style="background-color:#4f46e5;" disabled' : ''}>
                ${estDansLePanier ? 'Sélectionné ✓' : 'Sélectionner'}
            </button>
                    </div>`;
                    catalogueContainer.insertAdjacentHTML('beforeend', cardHtml);
                }
            });
    return; // Important pour sortir de la fonction
}


if (voyagesFiltrés.length === 0) {
    catalogueContainer.innerHTML = "<p style='font-size: 0.875rem; color:#6b7280; font-weight:700;'>Aucune destination trouvée.</p>";
    return;
}

voyagesFiltrés.forEach(voyage => {
    const estDansLePanier = panier.some(item => item.id === voyage.id);
    const prixEntier = Math.round(voyage.prix);

    let visuelHtml = voyage.image_url 
    ? `<div class="placeholder-image-bloc" style="background-image: url('${voyage.image_url}'); background-size: cover; background-position: center;"></div>`
    : `<div class="placeholder-image-bloc ${voyage.couleur_css || 'bg-bali'}"></div>`;

    const cardHtml = `
        <div class="bloc-card">
            ${visuelHtml}
            <div class="card-header"><h3 class="card-title">${voyage.titre}</h3></div>
            <p class="card-description">${voyage.description}</p>
            <button class="btn-action-bloc" onclick="ajouterAuPanier(${voyage.id})" ${estDansLePanier ? 'style="background-color:#4f46e5;" disabled' : ''}>
                ${estDansLePanier ? 'Sélectionné ✓' : 'Sélectionner'}
            </button>
    </div>`;
    catalogueContainer.insertAdjacentHTML('beforeend', cardHtml);
});
}

    // AJOUT DESTINATION CLASSIQUE
window.ajouterAuPanier = function(id) {
    const voyageSelectionne = voyagesData.find(v => v.id == id);
    if (voyageSelectionne) { 
        voyageSelectionne.isPackage = false;
        panier = [voyageSelectionne]; 
        mettreAJourPanier(); 
        afficherCatalogue(); 
    }
}

    // AJOUT PACKAGE AU PANIER VISUEL
window.ajouterPackageLocal = function(id) {
    const packSelectionne = packagesData.find(p => p.id == id);
    if (packSelectionne) {
        packSelectionne.isPackage = true;
        panier = [packSelectionne];
        mettreAJourPanier();
        afficherCatalogue();
    }
}

window.retirerDuPanier = function() { panier = []; mettreAJourPanier(); afficherCatalogue(); }

function mettreAJourPanier() {
    panierContenu.innerHTML = ""; 
    const nbVoyageurs = parseInt(document.getElementById('search-voyageurs').value) || 1;

    if (panier.length === 0) {
        panierContenu.innerHTML = `<div style="text-align:center; padding:2rem 0; color:#9ca3af; font-size:0.75rem; font-weight:700;">Votre itinéraire est vide.</div>`;
        panierTotal.textContent = "0 €"; 
        panierStatut.textContent = "0 sélection";
        btnValiderPanier.disabled = true; 
        btnValiderPanier.textContent = "Continuer ➔";
        return;
    }

    const item = panier[0];

    if (item.isPackage) {
        const prixTotal = Math.round(item.prix) * nbVoyageurs;

    // Rendu basique, identique au style des destinations
        panierContenu.innerHTML = `
        <div class="panier-item-row" style="display:flex; justify-content:space-between; align-items:center; padding:10px; border:1px solid #e5e7eb; border-radius:8px; margin-bottom:10px;">
            <div>
                <p class="panier-item-name" style="margin:0; font-weight:600;">${item.titre}</p>
                <span class="panier-item-type" style="font-size:0.75rem; color:#6b7280;">Séjour complet</span>
            </div>
            <button onclick="retirerDuPanier()" style="background:none; border:none; color:#ef4444; font-weight:800; cursor:pointer;">✕</button>
        </div>`;

        panierTotal.textContent = prixTotal + " €";
        panierStatut.textContent = "1 package sélectionné";
        btnValiderPanier.textContent = "Voir le récapitulatif ➔";
    } else {
            // Affichage normal pour Destination
        panierContenu.innerHTML = `
            <div class="panier-item-row">
                <div><p class="panier-item-name">${item.titre}</p><span class="panier-item-type">Destination</span></div>
                <button onclick="retirerDuPanier()" style="background:none; border:none; color:#ef4444; font-weight:800; cursor:pointer;">✕</button></div>
        </div>`;
            panierTotal.textContent = "0 €"; // Destination = 0
            panierStatut.textContent = "1 destination sélectionnée";
            btnValiderPanier.textContent = "Continuer vers les transports ➔";
        }
        
        btnValiderPanier.disabled = false;
    }

    // LOGIQUE DU BOUTON CONTINUER
    btnValiderPanier.addEventListener('click', async () => {
        if (panier.length === 0) return;
        btnValiderPanier.textContent = "Initialisation..."; 
        btnValiderPanier.disabled = true;

        try {
            const nbVoyageurs = document.getElementById('search-voyageurs').value;
            await fetch(`stockage.php?action=set_voyageurs&nb=${nbVoyageurs}`);

            if (panier[0].isPackage) {
                // Si c'est un package, on appelle l'action complexe de stockage.php
                const reponse = await fetch(`stockage.php?action=ajouter_package&id=${panier[0].id}`);
                const data = await reponse.json();
                if (data.status === 'success') {
                    window.location.href = "panier.php"; // Redirige vers le carnet
                } else {
                    alert("Erreur lors de la création du package.");
                    btnValiderPanier.disabled = false;
                }
            } else {
                // Si c'est à la carte
                await fetch('stockage.php?action=retirer&id=all');
                const reponse = await fetch(`stockage.php?action=ajouter&id=${panier[0].id}&type=destination`);
                const data = await reponse.json();
                if (data.status === 'success') {
                    window.location.href = "transport.php"; 
                } else {
                    alert("Erreur réseau.");
                    btnValiderPanier.disabled = false;
                }
            }
        } catch (err) { 
            alert("Erreur serveur."); 
            btnValiderPanier.disabled = false; 
        }
    });

    window.ajouterPackageComplet = async function(id_package) {
        const btn = event.target;
        btn.textContent = "Création de l'itinéraire...";
        btn.disabled = true;

    // On enregistre les voyageurs d'abord
        const nbVoyageurs = document.getElementById('search-voyageurs').value;
        await fetch(`stockage.php?action=set_voyageurs&nb=${nbVoyageurs}`);

    // On dit au backend de construire tout le package
        fetch(`stockage.php?action=ajouter_package&id=${id_package}`)
        .then(r => r.json())
        .then(data => {
            if(data.status === 'success') {
                window.location.href = "panier.php"; // On redirige direct vers le carnet !
            }
        });
    }

    // Filtres
    document.querySelectorAll('.btn-categorie-rond').forEach(bouton => {
        bouton.addEventListener('click', () => {
            document.querySelectorAll('.btn-categorie-rond').forEach(b => { b.classList.remove('actif'); b.classList.add('inactif'); });
            bouton.classList.remove('inactif'); bouton.classList.add('actif');
            categorieActuelle = bouton.getAttribute('data-categorie'); afficherCatalogue();
        });
    });

    afficherCatalogue(); mettreAJourPanier();
</script>
<?php include 'footer.php'; ?>
<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'header.php'; 
include 'database.php';

$destinations = [];
if ($conn !== null) {
    try {
        $stmt = $conn->query("SELECT id, titre, description, prix, categorie, couleur_css, image_url FROM destinations ORDER BY id ASC");
        $destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) { echo "Erreur : " . $e->getMessage(); }
}
?>

<link rel="stylesheet" href="index.css">

<section class="search-section">
    <div class="search-container">
        <div class="title-bloc">
            <h1>Planifiez. Explorez. Vivez.</h1>
        </div>

        <form action="index.php" method="GET" class="search-form">
            <div class="champ-saisie-bloc">
                <label>Destination</label>
                <input type="text" id="search-destination" placeholder="Où allez-vous ?" oninput="afficherCatalogue()" />
            </div>
            <div class="champ-saisie-bloc">
                <label>Date de départ</label>
                <input type="date" id="search-date" value="2026-06-15"onchange="afficherCatalogue()" />
            </div>
            <div class="champ-saisie-bloc">
                <label>Voyageurs</label>
                <input type="number" id="search-voyageurs" min="1" max="10" value="1" onchange="afficherCatalogue()" />
            </div>
            <div class="champ-saisie-bloc" style="border:none;">
                <button type="button" id="btn-filtrer-recherche" class="btn-submit-recherche">Rechercher</button>
            </div>
        </form>
    </div>
</section>

<section class="filter-section">
    <div class="filter-container">
        <span class="filter-label">Catégories :</span>
        <div class="filter-item">
            <button class="btn-categorie-rond actif" data-categorie="tous">TO</button>
            <span class="filter-text">Tous</span>
        </div>
        <div class="filter-item">
            <button class="btn-categorie-rond inactif" data-categorie="plages">PL</button>
            <span class="filter-text">Plages</span>
        </div>
        <div class="filter-item">
            <button class="btn-categorie-rond inactif" data-categorie="montagnes">MO</button>
            <span class="filter-text">Montagnes</span>
        </div>
        <div class="filter-item">
            <button class="btn-categorie-rond inactif" data-categorie="urbain">UR</button>
            <span class="filter-text">Urbain</span>
        </div>
        <div class="filter-item">
            <button class="btn-categorie-rond inactif" data-categorie="atypiques">AT</button>
            <span class="filter-text">Atypiques</span>
        </div>
        <div class="filter-item">
            <button class="btn-categorie-rond inactif" data-categorie="aventures">AV</button>
            <span class="filter-text">Aventures</span>
        </div>
        <div class="filter-item">
            <button class="btn-categorie-rond inactif" data-categorie="detente">DE</button>
            <span class="filter-text">Détente</span>
        </div>
        <div class="filter-item">
            <button class="btn-categorie-rond inactif" data-categorie="culture">CU</button>
            <span class="filter-text">Culture</span>
        </div>
        <div class="filter-item">
            <button class="btn-categorie-rond inactif" data-categorie="gastronomie">GA</button>
            <span class="filter-text">Gastronomie</span>
        </div>
    </div>
</section>

<section class="main-content-section">
    <div class="main-grid">
        <div class="col-catalogue">
            <div class="bloc-title">
                <h2>Destinations</h2>
            </div>
            <div class="cards-grid" id="catalogue-voyages"></div>
        </div>

        <div>
            <div class="bloc-title">
                <h2>Mon itinéraire</h2>
            </div>
            <div class="panier-container">
                <p class="panier-sub" id="panier-statut">0 destination sélectionnée</p>
                <div class="panier-items-list" id="panier-contenu"></div>
                <div class="panier-total-row">
                    <span class="total-label">Prix total estimé</span>
                    <span class="total-price" id="panier-total">0 €</span>
                </div>
                <button class="btn-panier-main" id="btn-valider-panier" disabled>Voir tout mon itinéraire</button>
            </div>
        </div>
    </div>
</section>

<script>
    const voyagesData = <?php echo json_encode($destinations); ?>;
    let panier = [];
    let categorieActuelle = "tous";

    const catalogueContainer = document.getElementById('catalogue-voyages');
    const panierContenu = document.getElementById('panier-contenu');
    const panierTotal = document.getElementById('panier-total');
    const panierStatut = document.getElementById('panier-statut');
    const btnValiderPanier = document.getElementById('btn-valider-panier');

    function afficherCatalogue() {
    catalogueContainer.innerHTML = ""; 
    const texteRecherche = document.getElementById('search-destination').value.toLowerCase();
    // On ne garde que le match sur le titre
    const voyagesFiltrés = voyagesData.filter(v => {
        const matchCategorie = (categorieActuelle === "tous" || (v.categorie && v.categorie.split(',').includes(categorieActuelle)));
        // Filtrage strict sur le TITRE uniquement
        const matchTexte = v.titre.toLowerCase().includes(texteRecherche);
        
        return matchCategorie && matchTexte;
    });

    if (voyagesFiltrés.length === 0) {
        catalogueContainer.innerHTML = "<p>Aucune destination trouvée pour ces critères.</p>";
        return;
    }

        if (voyagesFiltrés.length === 0) {
            catalogueContainer.innerHTML = "<p style='font-size: 0.875rem; color:#6b7280; font-weight:700; text-transform:uppercase;'>Aucune destination ne correspond à vos critères.</p>";
            return;
        }

        voyagesFiltrés.forEach(voyage => {
            const estDansLePanier = panier.some(item => item.id === voyage.id);
            const prixEntier = Math.round(voyage.prix);
            let visuelHtml = voyage.image_url 
            ? `<div class="placeholder-image-bloc" style="background-image: url('${voyage.image_url}'); background-size: cover; background-position: center;"><span class="price-badge">${prixEntier} €</span></div>`
            : `<div class="placeholder-image-bloc ${voyage.couleur_css || 'bg-bali'}"><span class="price-badge">${prixEntier} €</span></div>`;

            const cardHtml = `
            <div class="bloc-card">
                ${visuelHtml}
                <div class="card-header">
                    <h3 class="card-title">${voyage.titre}</h3>
                </div>
                <p class="card-description">${voyage.description}</p>
                <button class="btn-action-bloc" onclick="ajouterAuPanier(${voyage.id})" ${estDansLePanier ? 'style="background-color:#4f46e5;" disabled' : ''}>
                    ${estDansLePanier ? 'Sélectionné ✓' : 'Sélectionner'}
                </button>
            </div>
            `;
            catalogueContainer.insertAdjacentHTML('beforeend', cardHtml);
        });
    }

    window.ajouterAuPanier = function(id) {
        const voyageSelectionne = voyagesData.find(v => v.id == id);
        if (voyageSelectionne) {
            panier = [voyageSelectionne];
            mettreAJourPanier();
            afficherCatalogue();
        }
    }

    window.retirerDuPanier = function(id) {
        panier = [];
        mettreAJourPanier();
        afficherCatalogue();
    }

    function mettreAJourPanier() {
        panierContenu.innerHTML = ""; 
        if (panier.length === 0) {
            panierContenu.innerHTML = `<div style="text-align:center; padding:2rem 0; color:#9ca3af; font-size:0.75rem; font-weight:700; text-transform:uppercase;">Votre itinéraire est vide.<br>Sélectionnez une destination.</div>`;
            panierTotal.textContent = "0 €";
            panierStatut.textContent = "0 destination sélectionnée";
            btnValiderPanier.disabled = true;
            return;
        }

        let total = 0;
        panier.forEach(item => {
            const prixItem = Math.round(item.prix);
            total += prixItem;
            const itemHtml = `
            <div class="panier-item-row">
                <div>
                    <p class="panier-item-name">${item.titre}</p>
                    <span class="panier-item-type" style="text-transform:uppercase;">Destination Principale</span>
                </div>
                <div style="display:flex; align-items:center; gap:0.5rem;">
                    <span class="panier-item-price">${prixItem} €</span>
                    <button onclick="retirerDuPanier(${item.id})" style="background:none; border:none; color:#ef4444; font-weight:800; cursor:pointer; font-size:11px;">✕</button>
                </div>
            </div>
            `;
            panierContenu.insertAdjacentHTML('beforeend', itemHtml);
        });
        panierTotal.textContent = total + " €";
        panierStatut.textContent = `${panier.length} destination configurée`;
        btnValiderPanier.disabled = false;
    }

    btnValiderPanier.addEventListener('click', async () => {
        if (panier.length === 0) return;
        btnValiderPanier.textContent = "Initialisation de l'itinéraire...";
        btnValiderPanier.disabled = true;

        try {
            await fetch('stockage.php?action=retirer&id=all');
            // MODIFICATION ICI : &type=destination ajouté
            const reponse = await fetch(`stockage.php?action=ajouter&id=${panier[0].id}&type=destination`);
            const data = await reponse.json();
            
            if (data.status === 'success') {
                window.location.href = "transport.php"; 
            } else {
                alert("Erreur lors du stockage.");
                btnValiderPanier.textContent = "Voir tout mon itinéraire";
                btnValiderPanier.disabled = false;
            }
        } catch (err) {
            console.error(err);
            alert("Erreur réseau.");
            btnValiderPanier.textContent = "Voir tout mon itinéraire";
            btnValiderPanier.disabled = false;
        }
    });

    document.querySelectorAll('.btn-categorie-rond').forEach(bouton => {
        bouton.addEventListener('click', () => {
            document.querySelectorAll('.btn-categorie-rond').forEach(b => {
                b.classList.remove('actif'); b.classList.add('inactif');
            });
            bouton.classList.remove('inactif'); bouton.classList.add('actif');
            categorieActuelle = bouton.getAttribute('data-categorie');
            afficherCatalogue();
        });
    });

    document.getElementById('btn-filtrer-recherche').addEventListener('click', afficherCatalogue);
    afficherCatalogue();
    mettreAJourPanier();
</script>

<?php include 'footer.php'; ?>
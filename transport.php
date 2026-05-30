<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'header.php'; 
include 'database.php'; 

$dest_id = $_SESSION['destination_id'] ?? null;
$nb_voyageurs = $_SESSION['voyageurs'] ?? 1; 
$moyen_transport = [];

if ($conn && $dest_id) {
    try {
        $stmt = $conn->prepare("SELECT id, titre, description, prix, image_url, capacite_max, date_debut_dispo, date_fin_dispo FROM transports WHERE id_destination = :dest ORDER BY id ASC");
        $stmt->execute(['dest' => $dest_id]);
        $moyen_transport = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $exception) {}
} else {
    header("Location: index.php"); exit;
}
?>

<link rel="stylesheet" href="index.css">

<section class="search-section">
    <div class="search-container">
        <a href="index.php" style="display:inline-block; margin-bottom:10px; color:#4f46e5; font-weight:bold; text-decoration:none;">← Changer de destination</a>
        <div class="title-bloc"><h1>Vos Transferts & Vols Privés</h1></div>
        
        <form class="search-form">
            <div class="champ-saisie-bloc">
                <label>Recherche (Nom du transport)</label>
                <input type="text" id="search-nom" placeholder="..." oninput="afficherCatalogue()" />
            </div>
            <div class="champ-saisie-bloc">
                <label>Gamme</label>
                <select id="search-gamme" onchange="afficherCatalogue()">
                    <option value="tous">Toutes les options</option>
                    <option value="vol">Vols & Jets</option>
                    <option value="hélico">Hélicoptère</option>
                    <option value="voiture">Voitures Privées</option>
                    <option value="train">Trains</option>
                </select>
            </div>
        </form>
    </div>
</section>

<section class="main-content-section">
    <div class="main-grid">
        <div class="col-catalogue">
            <div class="bloc-title"><h2>Options pour <?= $nb_voyageurs ?> voyageur(s)</h2></div>
            <div class="cards-grid" id="catalogue-voyages"></div>
        </div>

        <div>
            <div class="bloc-title"><h2>Mon itinéraire</h2></div>
            <div class="panier-container">
                <p class="panier-sub" id="panier-statut">0 transport configuré</p>
                <div class="panier-items-list" id="panier-contenu"></div>
                <div class="panier-total-row">
                    <span class="total-label">Prix total estimé</span>
                    <span class="total-price" id="panier-total">0 €</span>
                </div>
                <button class="btn-panier-main" id="btn-valider-panier" disabled>Continuer vers les hébergements ➔</button>
            </div>
        </div>
    </div>
</section>

<script>
    const voyagesData = <?php echo json_encode($moyen_transport); ?>;
    const voyageursSession = <?php echo $nb_voyageurs; ?>;
    let panier = [];

    const catalogueContainer = document.getElementById('catalogue-voyages');
    const panierContenu = document.getElementById('panier-contenu');
    const panierTotal = document.getElementById('panier-total');
    const btnValiderPanier = document.getElementById('btn-valider-panier');

    function afficherCatalogue() {
        catalogueContainer.innerHTML = ""; 
        const texteRecherche = document.getElementById('search-nom').value.toLowerCase();
        const filtreGamme = document.getElementById('search-gamme').value.toLowerCase();

        const voyagesFiltrés = voyagesData.filter(v => {
            // NOUVEAU : Filtre strict sur le Titre uniquement
            const matchTexte = v.titre.toLowerCase().includes(texteRecherche);
            // NOUVEAU : Filtre de Gamme
            const matchGamme = filtreGamme === 'tous' || v.titre.toLowerCase().includes(filtreGamme) || v.description.toLowerCase().includes(filtreGamme);
            // NOUVEAU : Capacité
            const matchCapacite = v.capacite_max == null || v.capacite_max >= voyageursSession;
            
            return matchTexte && matchGamme && matchCapacite;
        });

        if (voyagesFiltrés.length === 0) {
            catalogueContainer.innerHTML = "<p style='color:#6b7280; font-weight:700;'>Aucune option disponible.</p>";
            return;
        }

        voyagesFiltrés.forEach(voyage => {
            const estDansLePanier = panier.some(item => item.id === voyage.id);
            const prixEntier = Math.round(voyage.prix);

            let today = new Date().toISOString().split('T')[0];
            let minDate = voyage.date_debut_dispo ? voyage.date_debut_dispo : today;
            let maxDate = voyage.date_fin_dispo ? voyage.date_fin_dispo : '2030-12-31';
            
            let dispoHtml = voyage.date_debut_dispo 
                ? `<p style="font-size: 0.75rem; color: #059669; font-weight:bold; background:#d1fae5; padding:4px; border-radius:4px; text-align:center;">Dispo du ${voyage.date_debut_dispo} au ${voyage.date_fin_dispo}</p>`
                : `<p style="font-size: 0.75rem; color: #2563eb; font-weight:bold; background:#dbeafe; padding:4px; border-radius:4px; text-align:center;">Disponible toute l'année</p>`;

            let visuelHtml = voyage.image_url 
            ? `<div class="placeholder-image-bloc" style="background-image: url('${voyage.image_url}'); background-size: cover; background-position: center;"><span class="price-badge">${prixEntier} €</span></div>`
            : `<div class="placeholder-image-bloc bg-transport"><span class="price-badge">${prixEntier} €</span></div>`;

            const cardHtml = `
            <div class="bloc-card card-item">
                ${visuelHtml}
                <div class="card-header"><h3 class="card-title">${voyage.titre}</h3></div>
                <p class="card-description">${voyage.description}</p>
                
                ${dispoHtml}
                <p style="font-size:0.7rem; color:#6b7280; margin-bottom:10px;">Capacité max : ${voyage.capacite_max || 'illimitée'} personnes</p>

                <div style="margin: 10px 0; display: flex; flex-direction: column; gap: 5px; font-size: 0.85rem;">
                    <label>Départ : <input type="date" class="date-debut" min="${minDate}" max="${maxDate}"></label>
                    <label>Retour : <input type="date" class="date-fin" min="${minDate}" max="${maxDate}"></label>
                </div>

                <button class="btn-action-bloc" onclick="ajouterAuPanier(${voyage.id}, 'transport', this, '${voyage.titre.replace(/'/g, "\\'")}', ${voyage.prix}, '${minDate}', '${maxDate}')" ${estDansLePanier ? 'style="background-color:#4f46e5;" disabled' : ''}>
                    ${estDansLePanier ? 'Sélectionné ✓' : 'Sélectionner ce transport'}
                </button>
            </div>
            `;
            catalogueContainer.insertAdjacentHTML('beforeend', cardHtml);
        });
    }

    function ajouterAuPanier(id, type, bouton, titre, prix, minDate, maxDate) {
        const parentCard = bouton.closest('.card-item');
        const dateDebut = parentCard.querySelector('.date-debut').value;
        const dateFin = parentCard.querySelector('.date-fin').value;

        if (!dateDebut || !dateFin) { alert("Veuillez sélectionner vos dates !"); return; }
        if (dateFin < dateDebut) { alert("La date de retour doit être après le départ !"); return; }
        if (dateDebut < minDate || dateFin > maxDate) { alert("Dates hors période de disponibilité !"); return; }

        bouton.textContent = "Ajout..."; bouton.disabled = true;

        fetch(`stockage.php?action=ajouter&id=${id}&type=${type}&date_debut=${dateDebut}&date_fin=${dateFin}`)
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    panier.push({ id: id, titre: titre, prix: prix, debut: dateDebut, fin: dateFin });
                    mettreAJourPanier(); afficherCatalogue();
                } else {
                    alert("Erreur serveur."); bouton.textContent = "Sélectionner"; bouton.disabled = false;
                }
            });
    }

    window.retirerDuPanier = function(id) {
        fetch(`stockage.php?action=retirer&id=${id}&type=transport`).then(r => r.json()).then(data => {
            if(data.status === 'success') { panier = panier.filter(item => item.id != id); mettreAJourPanier(); afficherCatalogue(); }
        });
    }

    function mettreAJourPanier() {
        panierContenu.innerHTML = ""; 
        if (panier.length === 0) {
            panierContenu.innerHTML = `<div style="text-align:center; padding:2rem 0; color:#9ca3af; font-size:0.75rem; font-weight:700;">Aucun transport.</div>`;
            panierTotal.textContent = "0 €"; btnValiderPanier.disabled = true; return;
        }
        let total = 0;
        panier.forEach(item => {
            const prixItem = Math.round(item.prix) * voyageursSession; total += prixItem;
            panierContenu.insertAdjacentHTML('beforeend', `
            <div class="panier-item-row" style="flex-direction:column; align-items:flex-start;">
                <div style="display:flex; justify-content:space-between; width:100%;">
                    <div><p class="panier-item-name">${item.titre}</p><span class="panier-item-type">TRANSPORT</span></div>
                    <div style="display:flex; align-items:center; gap:0.5rem;"><span class="panier-item-price">${prixItem} €</span>
                    <button onclick="retirerDuPanier(${item.id})" style="background:none; border:none; color:#ef4444; font-weight:800; cursor:pointer;">✕</button></div>
                </div>
                <div style="font-size:0.7rem; color:#6b7280; margin-top:5px;">Du ${item.debut} au ${item.fin}</div>
            </div>`);
        });
        panierTotal.textContent = total + " €"; btnValiderPanier.disabled = false;
        document.getElementById('panier-statut').textContent = `${panier.length} transport(s)`;
    }

    btnValiderPanier.addEventListener('click', () => { if (panier.length > 0) window.location.href = "hebergement.php"; });
    afficherCatalogue(); mettreAJourPanier();
</script>

<?php include 'footer.php'; ?>
<?php 
include 'header.php'; 
?>

    <link rel="stylesheet" href="index.css">

<section class="search-section">
    <div class="search-container">
        <div class="title-bloc">
            <h1>Planifiez. Explorez. Vivez.</h1>
            <p>Agence de voyages — Configuration d'itinéraires</p>
        </div>

        <form action="index.php" method="GET" class="search-form">
            <div class="champ-saisie-bloc">
                <label>Destination</label>
                <input type="text" name="destination" placeholder="Où allez-vous ?" />
            </div>
            <div class="champ-saisie-bloc">
                <label>Transport</label>
                <select name="transport">
                    <option value="avion">Avion</option>
                    <option value="train">Train</option>
                    <option value="bus">Bus</option>
                    <option value="voiture">Voiture</option>
                </select>
            </div>
            <div class="champ-saisie-bloc">
                <label>Dates & Voyageurs</label>
                <input type="text" placeholder="15 Juin 2026 — 2 adultes" />
            </div>
            <div>
                <button type="submit" class="btn-submit-recherche">Rechercher</button>
            </div>
        </form>
    </div>
</section>

<section class="filter-section">
    <div class="filter-container">
        <span class="filter-text" style="color: #9ca3af;">Catégories :</span>
        <div class="filter-item">
            <button class="btn-categorie-rond actif">PL</button>
            <span class="filter-text">Plages</span>
        </div>
        <div class="filter-item">
            <button class="btn-categorie-rond inactif">MO</button>
            <span class="filter-text">Montagnes</span>
        </div>
        <div class="filter-item">
            <button class="btn-categorie-rond inactif">AV</button>
            <span class="filter-text">Aventures</span>
        </div>
        <div class="filter-item">
            <button class="btn-categorie-rond inactif">DE</button>
            <span class="filter-text">Détente</span>
        </div>
    </div>
</section>

<section class="main-content-section">
    <div class="main-grid">
        
        <div class="col-catalogue">
            <div class="bloc-title">
                <h2>Destinations disponibles</h2>
            </div>
            
            <div class="cards-grid">
                <div class="bloc-card">
                    <div class="placeholder-image-bloc bg-bali">
                        <span class="price-badge">789 €</span>
                    </div>
                    <div class="card-header">
                        <h3 class="card-title">Bali, Indonésie</h3>
                    </div>
                    <p class="card-description">Vol régulier inclus au départ de Paris, hébergement en bord de mer.</p>
                    <button class="btn-action-bloc">Sélectionner cette brique</button>
                </div>

                <div class="bloc-card">
                    <div class="placeholder-image-bloc bg-transport">
                        <span class="price-badge">159 €</span>
                    </div>
                    <div class="card-header">
                        <h3 class="card-title">Ligne de Bus — Europe</h3>
                    </div>
                    <p class="card-description">Brique de transport terrestre reliant de manière fluide les grandes capitales.</p>
                    <button class="btn-action-bloc">Sélectionner ce transport</button>
                </div>
            </div>
        </div>

        <div>
            <div class="bloc-title">
                <h2>Mon itinéraire composé</h2>
            </div>
            <div class="panier-container">
                <div class="panier-item-row">
                    <div>
                        <p>Hôtel Tropical Resort</p>
                    </div>
                    <span class="panier-item-price">65 € / nuit</span>
                </div>
                <div class="panier-total-row">
                    <span class="total-label">Prix total estimé</span>
                    <span class="total-price">1509 €</span>
                </div>
                <button class="btn-panier-main">Voir tout mon itinéraire</button>
            </div>
        </div>

    </div>
</section>

<?php include 'footer.php'; ?>
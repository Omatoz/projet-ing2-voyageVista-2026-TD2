<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'header.php'; 
include 'database.php'; 

$choix = [];
$totalGlobal = 0;

if (isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
    if ($conn !== null) {
        foreach ($_SESSION['panier'] as $item) {
            $id = (int)$item['id'];
            $type = $item['type'];
            $sql = "";
            
            // Requêtes dynamiques selon la table
            if ($type === 'destination') {
                $sql = "SELECT titre, prix, 'Destination' as type_brique FROM destinations WHERE id = $id";
            } elseif ($type === 'transport') {
                $sql = "SELECT titre, prix, 'Transport' as type_brique FROM transports WHERE id = $id";
            } elseif ($type === 'hebergement') {
                $sql = "SELECT titre, prix_nuit as prix, 'Hébergement' as type_brique FROM hebergements WHERE id = $id";
            } elseif ($type === 'activite') {
                $sql = "SELECT titre, prix_ticket as prix, 'Activité' as type_brique FROM activites WHERE id = $id";
            }
            
            if ($sql !== "") {
                try {
                    $stmt = $conn->query($sql);
                    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $choix[] = $row;
                    }
                } catch(PDOException $exception) {
                    echo "Erreur de chargement pour $type: " . $exception->getMessage();
                }
            }
        }
    }
}

if (isset($_POST['action_recommencer'])) {
    $_SESSION['panier'] = [];
    unset($_SESSION['destination_id']);
    header("Location: index.php");
    exit;
}
?>

<link rel="stylesheet" href="index.css">

<section class="search-section">
    <div class="search-container" style="text-align: center;">
        <div class="title-bloc">
            <h1>Votre Carnet de Voyage Final</h1>
            <p style="color: #94a3b8; margin-top: 0.5rem;">Récapitulatif complet de vos prestations configurées</p>
        </div>
    </div>
</section>

<div style="max-width: 800px; margin: 3rem auto; padding: 0 1.5rem; font-family: sans-serif;">
    <div class="panier-container" style="background: white; border-radius: 12px; padding: 2.5rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);">
        
        <div class="panier-items-list" style="margin-bottom: 2rem;">
            <?php if (empty($choix)): ?>
                <div style="text-align:center; padding:3rem 0; color:#9ca3af; font-weight:700; text-transform:uppercase;">
                    Votre itinéraire est vide ou n'a pas été validé correctement.
                </div>
            <?php else: ?>
                <?php foreach ($choix as $brique): ?>
                    <?php 
                        $prixBrique = round($brique['prix']);
                        $totalGlobal += $prixBrique;
                    ?>
                    <div class="panier-item-row" style="padding: 1.25rem 0; border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center; border-left: 4px solid #4f46e5; padding-left: 1rem; margin-bottom: 0.75rem; background: #fafafa; border-radius: 4px;">
                        <div>
                            <p class="panier-item-name" style="font-size: 1rem; font-weight: 700; margin: 0; color: #1f2937;"><?php echo htmlspecialchars($brique['titre']); ?></p>
                            <span class="panier-item-type" style="text-transform:uppercase; font-size: 10px; color:#4f46e5; font-weight: bold; letter-spacing: 0.05em;">
                                Catégorie : <?php echo htmlspecialchars($brique['type_brique']); ?>
                            </span>
                        </div>
                        <div>
                            <span class="panier-item-price" style="font-size: 1.1rem; font-weight: 800; color: #111827;"><?php echo $prixBrique; ?> €</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="panier-total-row" style="padding: 1.5rem 0; border-top: 2px dashed #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
            <span class="total-label" style="font-size: 1rem; font-weight: 700; color: #4b5563;">COÛT TOTAL ESTIMÉ</span>
            <span class="total-price" style="font-size: 1.75rem; font-weight: 800; color: #4f46e5;"><?php echo $totalGlobal; ?> €</span>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <form method="POST" action="panier.php" style="flex: 1;">
                <button type="submit" name="action_recommencer" class="btn-panier-main" style="background-color: #ef4444; width: 100%; border: none; padding: 1rem; color: white; font-weight: 700; border-radius: 8px; cursor: pointer;">
                    Annuler et recommencer
                </button>
            </form>
            <button onclick="window.print()" class="btn-panier-main" style="background-color: #10b981; flex: 1; border: none; padding: 1rem; color: white; font-weight: 700; border-radius: 8px; cursor: pointer;">
                Imprimer mon itinéraire
            </button>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
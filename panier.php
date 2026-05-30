<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'header.php'; 
include 'database.php'; 

$choix = [];
$totalGlobal = 0;
$nb_voyageurs = $_SESSION['voyageurs'] ?? 1;

if (isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
    if ($conn !== null) {
        foreach ($_SESSION['panier'] as $item) {
            $id = (int)$item['id'];
            $type = $item['type'];
            $sql = "";
            if ($type === 'destination') { $sql = "SELECT id, titre, prix, 'Destination' as type_brique, 'destination' as type_item FROM destinations WHERE id = $id"; }
            elseif ($type === 'transport') { $sql = "SELECT id, titre, prix, 'Transport' as type_brique, 'transport' as type_item FROM transports WHERE id = $id"; }
            elseif ($type === 'hebergement') { $sql = "SELECT id, titre, prix_nuit as prix, 'Hébergement' as type_brique, 'hebergement' as type_item FROM hebergements WHERE id = $id"; }
            elseif ($type === 'activite') { $sql = "SELECT id, titre, prix_ticket as prix, 'Activité' as type_brique, 'activite' as type_item FROM activites WHERE id = $id"; }
            elseif ($type === 'package' || $type === 'sejour') { 
                $sql = "SELECT id, titre, prix, 'Séjour' as type_brique, 'package' as type_item FROM sejours WHERE id = $id"; 
            }
            
            if ($sql !== "") {
                try {
                    $stmt = $conn->query($sql);
                    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { 
                        $row['date_debut'] = $item['date_debut'] ?? null;
                        $row['date_fin'] = $item['date_fin'] ?? null;
                        $prix_unitaire = round($row['prix']);

    // LOGIQUE DE CALCUL DU PRIX
                        if ($type === 'destination') {
                            $row['sous_total'] = 0;
                        } elseif ($type === 'package' || $type === 'sejour') {
        // Le prix du séjour est fixe (il englobe déjà tout, pas besoin de multiplier par nuit)
                            $row['sous_total'] = $prix_unitaire * $nb_voyageurs; 
                        } elseif ($type === 'hebergement') {
                            $date1 = new DateTime($row['date_debut']);
                            $date2 = new DateTime($row['date_fin']);
                            $diffNuits = $date1->diff($date2)->days ?: 1;
                            $row['sous_total'] = $prix_unitaire * $diffNuits;
                        } else {
                            $row['sous_total'] = $prix_unitaire * $nb_voyageurs;
                        }

                        $totalGlobal += $row['sous_total'];
                        $choix[] = $row; 
                    }

                    
                } catch(PDOException $exception) { echo "Erreur: " . $exception->getMessage(); }
            }
        }
    }
}

if (isset($_POST['action_recommencer'])) {
    $_SESSION['panier'] = [];
    unset($_SESSION['destination_id']);
    header("Location: index.php"); exit;
}
?>

<link rel="stylesheet" href="auth.css">
<style>
    .panier-item { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid #f3f4f6; }
    .payment-simulation { margin: 1.5rem 0; padding: 1rem; border: 1px dashed #d1d5db; border-radius: 8px; text-align: center; background: #fafafa; }
    .payment-icons { display: flex; justify-content: center; gap: 10px; margin-top: 10px; color: #9ca3af; font-size: 0.8rem; }
    .btn-print { background-color: #10b981; color: white; border: none; padding: 0.75rem; font-weight: 700; cursor: pointer; width: 100%; border-radius: 4px; font-size: 0.75rem; text-transform: uppercase; }
    .btn-print:hover { background-color: #059669; }
    .btn-delete-item { background: none; border: none; color: #ef4444; font-weight: 900; font-size: 1.1rem; cursor: pointer; margin-left: 10px; }
</style>

<div class="auth-page-wrapper">
    <div class="auth-card-strict">
        <h2 class="auth-header-title" style="margin-bottom: 0.5rem;">Mon Carnet de Voyage</h2>
        <p style="text-align:center; color:#4f46e5; font-weight:bold; margin-bottom:1.5rem;">Prévu pour <?= $nb_voyageurs ?> voyageur(s)</p>

        <div style="margin-bottom: 1.5rem;">
            <?php if (empty($choix)): ?>
                <p style="text-align:center; color:#9ca3af; font-size: 0.8rem;">Votre itinéraire est vide.</p>
                <div style="text-align:center; margin-top: 1rem;"><a href="index.php" class="btn-auth-submit" style="text-decoration:none;">Commencer un voyage</a></div>
            <?php else: ?>
                <?php foreach ($choix as $brique): ?>
                    <div class="panier-item">
                        <div>
                            <div style="font-size: 9px; font-weight: 800; color: #4f46e5; text-transform: uppercase;"><?= htmlspecialchars($brique['type_brique']) ?></div>
                            <div style="font-size: 0.85rem; font-weight: 600;"><?= htmlspecialchars($brique['titre']) ?></div>
                            <?php if ($brique['type_item'] === 'activite' && $brique['date_debut']): ?>
                                <div style="font-size: 0.7rem; color: #10b981; font-weight:bold;">Le <?= $brique['date_debut'] ?></div>
                            <?php elseif ($brique['date_debut']): ?>
                                <div style="font-size: 0.7rem; color: #10b981; font-weight:bold;">Du <?= $brique['date_debut'] ?> au <?= $brique['date_fin'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div style="display:flex; align-items:center;">
                            <span style="font-weight: 800; font-size: 0.9rem;">
                                <?= $brique['type_item'] === 'destination' ? 'Inclus' : $brique['sous_total'] . ' €' ?>
                            </span>
                            <?php if ($brique['type_item'] !== 'destination'): ?>
                                <button class="btn-delete-item" onclick="retirerItem(<?= $brique['id'] ?>, '<?= $brique['type_item'] ?>')">✕</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 2px solid #f3f4f6; padding-top: 1rem;">
            <span style="font-size: 0.8rem; font-weight: 700; color: #6b7280; text-transform: uppercase;">Total Estimé</span>
            <span style="font-size: 1.5rem; font-weight: 900; color: #4f46e5;"><?= $totalGlobal ?> €</span>
        </div>

        <?php if (!empty($choix)): ?>
            <div class="payment-simulation">
                <div style="font-size: 0.7rem; font-weight: 700; color: #6b7280;">MOYENS DE PAIEMENT SÉCURISÉS</div>
                <div class="payment-icons"><span>VISA</span> • <span>MASTERCARD</span> • <span>PAYPAL</span></div>
            </div>

            

            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="auth.php?redirect=panier.php" class="btn-auth-submit" style="display:block; text-align:center; text-decoration:none;">Se connecter pour réserver</a>
            <?php else: ?>
                <form method="POST" action="paiement.php">
                    <button type="submit" class="btn-auth-submit" style="width: 100%;">Procéder au paiement sécurisé</button>
                </form>
            <?php endif; ?>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-top: 0.5rem;">
                <button onclick="window.print()" class="btn-print">Imprimer</button>
                <form method="POST" action="panier.php" style="margin:0;"><button type="submit" name="action_recommencer" class="btn-auth-danger" style="margin:0; width:100%;">Annuler tout</button></form>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function retirerItem(id, type) {
        if (confirm("Retirer cet élément ?")) {
            fetch(`stockage.php?action=retirer&id=${id}&type=${type}`).then(r => r.json()).then(d => { if(d.status==='success') window.location.reload(); });
        }
    }
</script>
<?php include 'footer.php'; ?>
<?php
session_start();
include 'database.php';
include 'header.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header('Location: auth.php');
    exit;
}

$id_res = (int)$_GET['id'];

// 1. Récupération de la réservation
$stmt = $conn->prepare("SELECT r.*, d.titre as destination_nom 
                        FROM reservations r 
                        JOIN destinations d ON r.id_destination = d.id 
                        WHERE r.id = ? AND r.id_utilisateur = ?");
$stmt->execute([$id_res, $_SESSION['user_id']]);
$res = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$res) die("Réservation introuvable.");

// 2. Récupération des items
$stmtDetails = $conn->prepare("SELECT * FROM details_reservation WHERE id_reservation = ?");
$stmtDetails->execute([$id_res]);
$details = $stmtDetails->fetchAll(PDO::FETCH_ASSOC);

/**
 * Fonction améliorée et robuste
 */
function getDetailItem($conn, $type, $id) {
    // Correspondance type => table de la BDD
    $tables = [
        'transport'   => 'transports',
        'hebergement' => 'hebergements',
        'activite'    => 'activites',
        'destination' => 'destinations' // Fixe le bug du catamaran
    ];

    $table = $tables[$type] ?? null;
    
    if ($table) {
        $stmt = $conn->prepare("SELECT *, '$type' as type_final FROM $table WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return null;
}
?>

<link rel="stylesheet" href="auth.css"> <style>
    .reservation-header { background: #f3f4f6; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border-left: 5px solid #4f46e5; }
    .grid-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; }
    .card-item { border: 1px solid #e5e7eb; border-radius: 10px; padding: 1.5rem; background: #fff; transition: 0.3s; }
    .card-item:hover { box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
    .badge { padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; }
    .badge-transport { background: #dbeafe; color: #1e40af; }
    .badge-hebergement { background: #d1fae5; color: #065f46; }
    .badge-activite { background: #fef3c7; color: #92400e; }
    .badge-destination { background: #fee2e2; color: #991b1b; }
    .price-txt { font-size: 1.25rem; font-weight: 700; color: #4f46e5; margin-top: 1rem; }
</style>

<div class="auth-page-wrapper">
    <div class="auth-card-strict" style="max-width: 900px;">
        
        <a href="utilisateur.php" style="color: #6b7280; text-decoration: none; font-size: 0.9rem;">← Retour au tableau de bord</a>

        <div class="reservation-header" style="margin-top: 1rem;">
            <h1 style="margin: 0; font-size: 1.5rem;">Voyage à <?= htmlspecialchars($res['destination_nom']) ?></h1>
            <p style="margin: 5px 0 0; color: #4b5563;">Réservation #<?= $id_res ?> • Statut : <strong><?= ucfirst($res['statut']) ?></strong></p>
            <div class="price-txt"><?= number_format($res['total_prix'], 2) ?> €</div>
        </div>

        <div class="grid-container">
            <?php foreach ($details as $item): 
                $data = getDetailItem($conn, $item['type_item'], $item['id_item']);
                if ($data): 
                    $type = htmlspecialchars($item['type_item']);
            ?>
                <div class="card-item">
                    <span class="badge badge-<?= $type ?>"><?= $type ?></span>
                    <h3 style="margin: 0.75rem 0;"><?= htmlspecialchars($data['titre'] ?? 'Service') ?></h3>
                    <p style="font-size: 0.9rem; color: #6b7280; line-height: 1.5;">
                        <?= htmlspecialchars(substr($data['description'] ?? 'Pas de description.', 0, 100)) ?>...
                    </p>
                    <div style="font-weight: bold; color: #374151;">
                        Prix : <?= $data['prix'] ?? $data['prix_ticket'] ?? $data['prix_nuit'] ?? '0' ?> €
                    </div>
                </div>
            <?php endif; endforeach; ?>
        </div>

        <div style="margin-top: 2rem; text-align: center;">
            <button onclick="window.print()" class="btn-auth-submit" style="background-color: #10b981; border: none; cursor: pointer;">
                Imprimer l'itinéraire
            </button>
        </div>

    </div>
</div>

<?php include 'footer.php'; ?>
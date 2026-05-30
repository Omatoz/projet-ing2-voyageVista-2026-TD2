<?php
session_start();

include 'database.php';

// 1. Déplacez ceci tout en haut (après database.php)
$stmtUser = $conn->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmtUser->execute([$_SESSION['user_id']]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

// 1. Traitement modification Profil
if (isset($_POST['modifier_profil'])) {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

    if (!empty($nom) && !empty($prenom) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmtUpdate = $conn->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ? WHERE id = ?");
        $stmtUpdate->execute([$nom, $prenom, $email, $_SESSION['user_id']]);
        header("Location: utilisateur.php?message=profile_updated");
        exit;
    } else {
        $erreur_maj = "Veuillez remplir correctement tous les champs du profil.";
    }
}

// 2. Traitement modification Mot de passe
if (isset($_POST['changer_mdp'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Vérifier l'ancien mot de passe
    if (password_verify($old_password, $user['mot_de_passe'])) {
        if ($new_password === $confirm_password) {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmtUpdateMdp = $conn->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?");
            $stmtUpdateMdp->execute([$new_hash, $_SESSION['user_id']]);
            header("Location: utilisateur.php?message=password_updated");
            exit;
        } else {
            $erreur_mdp = "Les nouveaux mots de passe ne correspondent pas.";
        }
    } else {
        $erreur_mdp = "L'ancien mot de passe est incorrect.";
    }
}

// Récupération réservations
$stmtRes = $conn->prepare("SELECT r.*, d.titre as destination_nom, d.image_url as destination_img 
   FROM reservations r 
   JOIN destinations d ON r.id_destination = d.id 
   WHERE r.id_utilisateur = ? ORDER BY r.date_reservation DESC");
$stmtRes->execute([$_SESSION['user_id']]);
$reservations = $stmtRes->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: auth.php");
    exit;
}

if (isset($_POST['supprimer_compte'])) {
    try {
        // On commence une transaction pour garantir que tout soit bien supprimé
        $conn->beginTransaction();

        // Étape A : On supprime d'abord les réservations de l'utilisateur (pour éviter l'erreur de clé étrangère)
        $stmtDelRes = $conn->prepare("DELETE FROM reservations WHERE id_utilisateur = ?");
        $stmtDelRes->execute([$_SESSION['user_id']]);

        // Étape B : On supprime maintenant l'utilisateur
        $stmtDelUser = $conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmtDelUser->execute([$_SESSION['user_id']]);

        // On valide les deux suppressions
        $conn->commit();

        // On déconnecte et on redirige
        session_destroy();
        header("Location: auth.php?message=deleted");
        exit;
    } catch (Exception $e) {
        // Si une erreur survient, on annule tout
        $conn->rollBack();
        die("Erreur lors de la suppression : " . $e->getMessage());
    }
}

if (isset($_POST['supprimer_reservation'])) {
    $id_reservation = (int)$_POST['id_reservation'];
    
    // On vérifie que la réservation appartient bien à l'utilisateur connecté
    $stmt = $conn->prepare("DELETE FROM reservations WHERE id = ? AND id_utilisateur = ?");
    $stmt->execute([$id_reservation, $_SESSION['user_id']]);
    
    // On rafraîchit la page pour supprimer l'affichage
    header("Location: utilisateur.php");
    exit;
}

function getDetailItem($conn, $type, $id) {
    $tables = ['transport' => 'transports', 'hebergement' => 'hebergements', 'activite' => 'activites'];
    $table = $tables[$type] ?? null;
    return $table ? $conn->query("SELECT * FROM $table WHERE id = $id")->fetch(PDO::FETCH_ASSOC) : null;
}
?>

<link rel="stylesheet" href="auth.css">
<style>
    .dashboard-grid { display: grid; grid-template-columns: 320px 1fr; gap: 7rem; max-width: 1100px; margin: 2rem auto; padding: 0 1rem; }

    main { min-width: 0; }
    .res-item { background: #ffffff; border: 1px solid #e5e7eb; margin-bottom: 1.5rem; }
    .res-header { padding: 1.5rem; display: flex; align-items: center; cursor: pointer; }
    .res-thumb { width: 100px; height: 60px; object-fit: cover; border-radius: 4px; background-color: #f3f4f6; margin-right: 1.5rem; }
    .details-content { display: none; padding: 1.5rem; border-top: 1px solid #e5e7eb; background: #f9fafb; }
    .btn-auth-danger { background-color: #dc2626; color: white; font-weight: 700; font-size: 0.75rem; padding: 0.75rem 1rem; border: none; cursor: pointer; text-transform: uppercase; width: 100%; margin-top: 10px; }
    .btn-print { background-color: #4f46e5; color: white; font-weight: 700; font-size: 0.75rem; padding: 0.75rem 1rem; border: none; cursor: pointer; text-transform: uppercase; margin-bottom: 10px; }
    @media (max-width: 768px) { .dashboard-grid { grid-template-columns: 1fr; } }
</style>

<div class="dashboard-grid">
    <aside>
        <div class="auth-card-strict">
            <h2 class="auth-header-title" style="text-align: left; margin-bottom: 1.5rem;">Mon Compte</h2>

            <form method="POST" action="utilisateur.php">
                <div class="champ-saisie-auth" style="margin-bottom: 1rem;">
                    <label>Prénom</label>
                    <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required style="width: 100%; border:none; background:transparent; font-weight: 600;">
                </div>
                <div class="champ-saisie-auth" style="margin-bottom: 1rem;">
                    <label>Nom</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required style="width: 100%; border:none; background:transparent; font-weight: 600;">
                </div>
                <div class="champ-saisie-auth" style="margin-bottom: 1rem;">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required style="width: 100%; border:none; background:transparent; font-weight: 600;">
                </div>
                <button type="submit" name="modifier_profil" class="btn-auth-submit" style="width: 100%; border:none; margin-bottom: 10px; cursor:pointer;">Enregistrer les modifications</button>
            </form>

            <hr style="margin: 2rem 0; border: none; border-top: 1px solid #e5e7eb;">

            <h3 style="font-size: 0.9rem; margin-bottom: 1rem;">Changer le mot de passe</h3>
            <?php if (isset($erreur_mdp)): ?><p style="color: red; font-size: 0.8rem; margin-bottom: 10px;"><?= $erreur_mdp ?></p><?php endif; ?>

            <form method="POST" action="utilisateur.php">
                <div class="champ-saisie-auth" style="margin-bottom: 0.5rem;">
                    <input type="password" name="old_password" placeholder="Ancien mot de passe" required style="width: 100%; border:none; background:transparent;">
                </div>
                <div class="champ-saisie-auth" style="margin-bottom: 0.5rem;">
                    <input type="password" name="new_password" placeholder="Nouveau mot de passe" required style="width: 100%; border:none; background:transparent;">
                </div>
                <div class="champ-saisie-auth" style="margin-bottom: 1rem;">
                    <input type="password" name="confirm_password" placeholder="Confirmer nouveau" required style="width: 100%; border:none; background:transparent;">
                </div>
                <button type="submit" name="changer_mdp" class="btn-auth-submit" style="width: 100%; border:none; margin-bottom: 10px; cursor:pointer;">Mettre à jour le mot de passe</button>
            </form>

            <hr style="margin: 2rem 0; border: none; border-top: 1px solid #e5e7eb;">
            
            <a href="utilisateur.php?action=logout" class="btn-auth-submit" style="display:block; text-align:center; text-decoration:none; background-color:#6b7280; margin-bottom:10px;">Se déconnecter</a>

            <form method="POST" action="utilisateur.php" onsubmit="return confirm('Attention : suppression définitive ?');">
                <button type="submit" name="supprimer_compte" class="btn-auth-danger">Supprimer mon compte</button>
            </form>
        </div>
    </aside>

    <main>
        <h2 style="text-transform: uppercase; font-size: 1.25rem; font-weight: 900; margin-bottom: 1.5rem;">Mes Réservations</h2>
        
        <?php foreach ($reservations as $res): 
            $stmtD = $conn->prepare("SELECT * FROM details_reservation WHERE id_reservation = ?");
            $stmtD->execute([$res['id']]);
            $details = $stmtD->fetchAll(PDO::FETCH_ASSOC);
            
            // Calcul dynamique du prix total
            $total_calcule = 0;
            $items_data = [];
            foreach ($details as $item) {
                $data = getDetailItem($conn, $item['type_item'], $item['id_item']);
                if ($data) {
                    $prix = (float)($data['prix'] ?? $data['prix_ticket'] ?? $data['prix_nuit'] ?? 0);
                    $total_calcule += $prix;
                    $items_data[] = ['data' => $data, 'type' => $item['type_item'], 'prix' => $prix];
                }
            }
            ?>
            <div class="res-item">
                <div class="res-header" onclick="toggleDetails(<?= $res['id'] ?>)">
                    <img src="<?= htmlspecialchars($res['destination_img'] ?? 'default.jpg') ?>" class="res-thumb">
                    <div style="flex-grow: 1;">
                        <h4 style="margin:0; text-transform: uppercase; font-size: 0.9rem;"><?= htmlspecialchars($res['destination_nom']) ?></h4>
                        <small style="color: #9ca3af;"><?= ucfirst($res['statut']) ?> • <strong><?= number_format($total_calcule, 2) ?> €</strong></small>
                    </div>
                    <span style="font-size: 0.8rem; color: #4f46e5; font-weight: bold;">VOIR DÉTAILS</span>
                </div>
                
                <div id="details-<?= $res['id'] ?>" class="details-content">
                    <div id="print-area-<?= $res['id'] ?>">
                        <h3 style="margin-top:0;">Itinéraire : <?= htmlspecialchars($res['destination_nom']) ?></h3>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                            <?php foreach ($items_data as $i): ?>
                                <div style="background: white; padding: 10px; border: 1px solid #e5e7eb;">
                                    <strong><?= htmlspecialchars($i['data']['titre']) ?></strong><br>
                                    <small style="color:#6b7280; text-transform:uppercase;"><?= htmlspecialchars($i['type']) ?></small><br>
                                    <strong><?= number_format($i['prix'], 2) ?> €</strong>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p style="font-weight:bold; font-size: 1.1rem;">TOTAL : <?= number_format($total_calcule, 2) ?> €</p>
                    </div>
                    
                    <form method="POST" onsubmit="return confirm('Voulez-vous vraiment annuler ce voyage ?');">
                        <input type="hidden" name="id_reservation" value="<?= $res['id'] ?>">
                        <button type="submit" name="supprimer_reservation" class="btn-auth-danger">Annuler cette réservation</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </main>
</div>

<script>
    function toggleDetails(id) {
        const content = document.getElementById('details-' + id);
        content.style.display = (content.style.display === 'block') ? 'none' : 'block';
    }
</script>

<?php include 'footer.php'; ?>
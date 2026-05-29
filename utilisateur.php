<?php
session_start();
include 'database.php';
include 'header.php';

// 1. Protection : redirection si non connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

// 2. Traitement des actions (déconnexion, suppression, etc.)
if (isset($_GET['action'])) {
    // Action de déconnexion
    if ($_GET['action'] === 'deconnexion') {
        session_destroy();
        header('Location: index.php');
        exit;
    }
    
    // Action de suppression de compte
    if ($_GET['action'] === 'supprimer_compte') {
        $stmt = $conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        session_destroy();
        header('Location: index.php');
        exit;
    }
}

// 3. Récupération des infos utilisateur pour affichage
$stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="auth.css">

<div class="auth-page-wrapper">
    <div class="auth-card-strict">
        <h2 class="auth-header-title">Mon Espace</h2>
        <p class="auth-header-sub">Bienvenue <?= htmlspecialchars($user['prenom']); ?></p>

        <div style="display: flex; flex-direction: column; gap: 1rem; margin-top: 2rem;">
            
            <a href="utilisateur.php?action=deconnexion" class="btn-auth-submit" style="text-align:center; text-decoration:none; background-color: #6b7280;">
                Se déconnecter
            </a>

            <a href="utilisateur.php?action=supprimer_compte" 
               onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')" 
               style="color: red; font-size: 0.8rem; text-align: center; margin-top: 1rem;">
                Supprimer mon compte définitivement
            </a>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
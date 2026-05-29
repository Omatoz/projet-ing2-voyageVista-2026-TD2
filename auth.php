<?php
session_start();
include 'header.php'; 
include 'database.php'; 

$message_erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if ($conn !== null) {
        // 1. Chercher l'utilisateur par email
        // ATTENTION : Utilisez le nom de colonne exact de votre base (souvent 'mot_de_passe')
        $stmt = $conn->prepare("SELECT id, mot_de_passe FROM utilisateurs WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Vérifier si l'utilisateur existe ET si le mot de passe est correct
        // On utilise $user['mot_de_passe'] car c'est le nom dans le SELECT ci-dessus
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            // Connexion réussie
            $_SESSION['user_id'] = $user['id'];
            
            // Redirection dynamique
            $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
            header("Location: $redirect");
            exit;
        } else {
            // Identifiants incorrects
            $message_erreur = "Identifiant ou mot de passe incorrect.";
        }
    }
}
?>

<link rel="stylesheet" href="auth.css">


<div class="auth-page-wrapper">
    <div class="auth-card-strict">

        <div>
            <h2 class="auth-header-title">Connexion</h2>
            <p class="auth-header-sub">Veuillez entrer vos identifiants d'accès</p>
            <?php if (!empty($message_erreur)): ?>
                <p style="color: red; font-size: 0.9rem; margin-top: 10px;">
                    <?php echo $message_erreur; ?>
                </p>
            <?php endif; ?>
        </div>

        <form action="auth.php?redirect=<?php echo urlencode($_GET['redirect'] ?? 'utilisateur.php'); ?>" method="POST" class="auth-form-vertical">

            <div class="champ-saisie-auth">
                <label>Identifiant / Email</label>
                <input type="email" name="email" required placeholder="nom@domaine.com" />
            </div>

            <div class="champ-saisie-auth">
                <label>Mot de passe</label>
                <input type="password" name="password" required placeholder="••••••••" />
            </div>

            <button type="submit" class="btn-auth-submit">Valider la connexion</button>
            
        </form>

        <div class="auth-footer-box">
            <p class="auth-meta-text">
                Pas encore enregistré ? <a href="inscription.php" class="auth-link-action">Créer un compte</a>
            </p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
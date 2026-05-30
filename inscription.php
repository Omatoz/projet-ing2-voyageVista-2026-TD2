<?php
include 'database.php';
include 'header.php'; 

$erreur = "";
$succes = "";

if (isset($_SESSION['user_id'])) {
    header('Location: utilisateur.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = strip_tags(trim($_POST['nom']));
    $prenom = strip_tags(trim($_POST['prenom']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
        $erreur = "Veuillez remplir tous les champs obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "L'adresse email n'est pas valide.";
    } else {
        if ($conn !== null) {
            try {
                $checkEmail = $conn->prepare("SELECT id FROM utilisateurs WHERE email = :email");
                $checkEmail->execute(['email' => $email]);
                
                if ($checkEmail->rowCount() > 0) {
                    $erreur = "Cette adresse email est déjà associée à un compte.";
                } else {
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    $insert = $conn->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role, date_creation) VALUES (:nom, :prenom, :email, :mdp, 'client', NOW())");
                    $insert->execute(['nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'mdp' => $passwordHash]);
                    $succes = "Votre compte a été créé avec succès !";
                }
            } catch (PDOException $e) {
                $erreur = "Erreur technique : " . $e->getMessage();
            }
        }
    }
}
?>

<link rel="stylesheet" href="auth.css">

<div class="auth-page-wrapper">
    <div class="auth-card-strict">
        
        <div style="text-align: center;">
            <h2 class="auth-header-title">Inscription</h2>
            <p class="auth-header-sub">Rejoignez VoyageVista</p>
        </div>

        <?php if (!empty($erreur)): ?>
            <p style="color: red; font-size: 0.8rem; text-align: center;"><?= $erreur; ?></p>
        <?php endif; ?>

        <?php if (!empty($succes)): ?>
            <p style="color: green; font-size: 0.8rem; text-align: center;"><?= $succes; ?> <br> <a href="auth.php">Se connecter</a></p>
        <?php endif; ?>

        <form action="inscription.php" method="POST" class="auth-form-vertical">
            
            <div class="champ-saisie-auth">
                <label>Nom</label>
                <input type="text" name="nom" required placeholder="Votre nom" value="<?= isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : '' ?>">
            </div>

            <div class="champ-saisie-auth">
                <label>Prénom</label>
                <input type="text" name="prenom" required placeholder="Votre prénom" value="<?= isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : '' ?>">
            </div>

            <div class="champ-saisie-auth">
                <label>Adresse Email</label>
                <input type="email" name="email" required placeholder="nom@domaine.com" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>

            <div class="champ-saisie-auth">
                <label>Mot de passe</label>
                <input type="password" name="password" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn-auth-submit">Créer mon compte</button>
        </form>

        <div class="auth-footer-box">
            <p class="auth-meta-text">
                Déjà inscrit ? <a href="auth.php" class="auth-link-action">Se connecter ici</a>
            </p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
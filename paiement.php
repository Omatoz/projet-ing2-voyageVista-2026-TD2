<?php
session_start();
include 'header.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['panier'])) {
    header('Location: panier.php');
    exit;
}
?>

<link rel="stylesheet" href="auth.css">
<style>
    .payment-container { max-width: 500px; margin: 3rem auto; background: white; padding: 2rem; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
    .card-element { background: #f9fafb; border: 1px solid #d1d5db; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; }
    .input-row { display: flex; gap: 1rem; }
    .input-row > div { flex: 1; }
</style>

<div class="auth-page-wrapper" style="align-items: flex-start;">
    <div class="payment-container">
        <h2 style="text-transform: uppercase; font-size: 1.25rem; font-weight: 900; margin-bottom: 0.5rem;">Paiement Sécurisé</h2>
        <p style="color: #6b7280; font-size: 0.85rem; margin-bottom: 2rem;">Veuillez saisir vos coordonnées bancaires pour finaliser la réservation.</p>

        <form action="reservation.php" method="POST">
            <div class="card-element">
                <div class="champ-saisie-auth" style="margin-bottom: 1rem;">
                    <label>Nom sur la carte</label>
                    <input type="text" name="cc_name" required placeholder="Ex: Jean Dupont" />
                </div>
                
                <div class="champ-saisie-auth" style="margin-bottom: 1rem;">
                    <label>Numéro de carte</label>
                    <input type="text" name="cc_number" required placeholder="0000 0000 0000 0000" maxlength="19" />
                </div>

                <div class="input-row">
                    <div class="champ-saisie-auth">
                        <label>Date d'expiration</label>
                        <input type="text" name="cc_exp" required placeholder="MM/YY" maxlength="5" />
                    </div>
                    <div class="champ-saisie-auth">
                        <label>CVC</label>
                        <input type="text" name="cc_cvc" required placeholder="123" maxlength="3" />
                    </div>
                </div>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.5rem;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/4/41/Visa_Logo.png" height="20" alt="Visa">
                <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" height="25" alt="Mastercard">
            </div>

            <button type="submit" class="btn-auth-submit" style="width: 100%; background-color:#10b981;">Payer et Confirmer</button>
            <a href="panier.php" style="display:block; text-align:center; margin-top:1rem; font-size:0.8rem; color:#6b7280; text-decoration:none;">← Retour au carnet de voyage</a>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
<?php
session_start();
include 'database.php';

// Sécurité : Vérifier si connecté
if (!isset($_SESSION['user_id'])) {
    // Redirige vers auth en lui disant de revenir ici après
    header('Location: auth.php?redirect=panier.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['panier'])) {
    try {
        $conn->beginTransaction();

        // 1. Calcul du prix total (Exemple simplifié)
        $prixTotal = 0;
        foreach ($_SESSION['panier'] as $item) {
            // Ici, faites une requête SELECT pour récupérer le prix réel en base
            // Pour l'exemple, supposons que vous avez le prix dans votre session ou une requête
            $prixTotal += 100; // À remplacer par la vraie valeur
        }

        // 2. Insertion réservation
        $dest_id = $_SESSION['destination_id'] ?? null;
        $sqlRes = "INSERT INTO reservations (id_utilisateur, id_destination, total_prix, statut) VALUES (?, ?, ?, 'en attente')";
        $stmtRes = $conn->prepare($sqlRes);
        $stmtRes->execute([$_SESSION['user_id'], $dest_id, $prixTotal]);
        
        $idReservation = $conn->lastInsertId();

        // 3. Insertion détails
        $sqlDetail = "INSERT INTO details_reservation (id_reservation, type_item, id_item) VALUES (?, ?, ?)";
        $stmtDetail = $conn->prepare($sqlDetail);
        foreach ($_SESSION['panier'] as $item) {
            $stmtDetail->execute([$idReservation, $item['type'], $item['id']]);
        }

        $conn->commit();
        
        // 4. Nettoyage
        $_SESSION['panier'] = [];
        unset($_SESSION['destination_id']);
        
        header('Location: utilisateur.php?success=1');
        exit;
        
    } catch (Exception $e) {
        $conn->rollBack();
        die("Erreur critique : " . $e->getMessage());
    }
} else {
    header('Location: panier.php');
}
?>
<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php?redirect=panier.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['panier'])) {
    try {
        $conn->beginTransaction();

        $prixTotal = 0;
        $nb_voyageurs = $_SESSION['voyageurs'] ?? 1;

        foreach ($_SESSION['panier'] as $item) {
            $id = $item['id'];
            $type = $item['type'];
            
            $table = ($type === 'hebergement') ? 'hebergements' : $type . 's';
            $colPrix = ($type === 'hebergement') ? 'prix_nuit' : (($type === 'activite') ? 'prix_ticket' : 'prix');
            
            $stmt = $conn->prepare("SELECT $colPrix FROM $table WHERE id = ?");
            $stmt->execute([$id]);
            $res = $stmt->fetch();
            if ($res) {
                // Calcul selon le type
                if ($type === 'hebergement') {
                    $d1 = new DateTime($item['date_debut']);
                    $d2 = new DateTime($item['date_fin']);
                    $nuits = $d1->diff($d2)->days ?: 1;
                    $prixTotal += ($res[$colPrix] * $nuits);
                } elseif ($type !== 'destination') {
                    $prixTotal += ($res[$colPrix] * $nb_voyageurs);
                }
            }
        }

        // Insertion réservation AVEC nb_voyageurs
        $dest_id = $_SESSION['destination_id'] ?? null;
        $sqlRes = "INSERT INTO reservations (id_utilisateur, id_destination, total_prix, statut, nb_voyageurs) VALUES (?, ?, ?, 'en attente', ?)";
        $stmtRes = $conn->prepare($sqlRes);
        $stmtRes->execute([$_SESSION['user_id'], $dest_id, $prixTotal, $nb_voyageurs]);
        $idReservation = $conn->lastInsertId();

        // Insertion détails
        $sqlDetail = "INSERT INTO details_reservation (id_reservation, type_item, id_item, date_debut, date_fin) VALUES (?, ?, ?, ?, ?)";
        $stmtDetail = $conn->prepare($sqlDetail);
        foreach ($_SESSION['panier'] as $item) {
            $stmtDetail->execute([$idReservation, $item['type'], $item['id'], $item['date_debut'] ?? null, $item['date_fin'] ?? null]);
        }

        // CRÉATION DE LA NOTIFICATION (Pour la grille d'évaluation)
        $sqlNotif = "INSERT INTO notifications (id_utilisateur, titre, message) VALUES (?, 'Réservation confirmée !', 'Votre voyage a bien été enregistré. Préparez vos bagages !')";
        
// Exemple à placer après l'insertion réussie de la réservation dans reservation.php
        $stmtNotif = $conn->prepare("INSERT INTO notifications (id_utilisateur, titre, message) VALUES (?, ?, ?)");
        $stmtNotif->execute([$_SESSION['user_id'], "Confirmation de réservation", "Votre voyage a bien été enregistré. Merci de votre confiance !"]);

        $conn->commit();
        
        $_SESSION['panier'] = [];
        unset($_SESSION['destination_id']);
        
        header('Location: utilisateur.php?success=1');
        exit;

    } catch (Exception $e) {
        $conn->rollBack();
        echo "Erreur lors de la réservation : " . $e->getMessage();
    }
} else {
    header('Location: panier.php');
    exit;
}
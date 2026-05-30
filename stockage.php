<?php
session_start();
include 'database.php'; 

header('Content-Type: application/json');

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    // 1. Définir le nombre de voyageurs
    if ($action === 'set_voyageurs' && isset($_GET['nb'])) {
        $_SESSION['voyageurs'] = intval($_GET['nb']);
        echo json_encode(['status' => 'success']);
        exit;
    }
    
    // 2. Retirer un item ou tout le panier
    if ($action === 'retirer' && isset($_GET['id'])) {
        if ($_GET['id'] === 'all') {
            $_SESSION['panier'] = [];
            unset($_SESSION['destination_id']); 
        } else {
            $id = intval($_GET['id']);
            $type = $_GET['type'];
            $_SESSION['panier'] = array_filter($_SESSION['panier'], function($v) use ($id, $type) {
                return !($v['id'] == $id && $v['type'] == $type);
            });
            $_SESSION['panier'] = array_values($_SESSION['panier']); 
        }
        echo json_encode(['status' => 'success', 'panier' => $_SESSION['panier']]);
        exit;
    }

    // 3. LOGIQUE DYNAMIQUE : AJOUTER UN PACKAGE
if ($action === 'ajouter_package' && isset($_GET['id'])) {
    $id_sejour = intval($_GET['id']);
    
    $stmt = $conn->prepare("SELECT * FROM sejours WHERE id = ?");
    $stmt->execute([$id_sejour]);
    $package = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($package) {
        // On vide le panier avant d'ajouter le package complet
        $_SESSION['panier'] = []; 
        
        // On récupère les composants
        $stmtComp = $conn->prepare("SELECT type_item, id_item, date_debut, date_fin FROM sejours_composants WHERE id_sejour = ?");
        $stmtComp->execute([$id_sejour]);
        $composants = $stmtComp->fetchAll(PDO::FETCH_ASSOC);

        foreach ($composants as $c) {
            $_SESSION['panier'][] = [
                'id'         => $c['id_item'],
                'type'       => $c['type_item'],
                'date_debut' => $c['date_debut'],
                'date_fin'   => $c['date_fin']
            ];
        }
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Séjour introuvable']);
    }
    exit;
}
    // 4. AJOUT CLASSIQUE (À la carte)
    if ($action === 'ajouter' && isset($_GET['id']) && isset($_GET['type'])) {
        $id = intval($_GET['id']);
        $type = $_GET['type'];
        $date_debut = $_GET['date_debut'] ?? null;
        $date_fin = $_GET['date_fin'] ?? null;

        if ($type === 'destination') { $_SESSION['destination_id'] = $id; }

        // Vérification conflits de dates
        if ($type === 'hebergement' && $date_debut && $date_fin) {
            $new_start = strtotime($date_debut);
            $new_end = strtotime($date_fin);
            foreach ($_SESSION['panier'] as $p) {
                if ($p['type'] === 'hebergement' && $p['date_debut'] && $p['date_fin']) {
                    $exist_start = strtotime($p['date_debut']);
                    $exist_end = strtotime($p['date_fin']);
                    if (($new_start < $exist_end) && ($new_end > $exist_start)) {
                        echo json_encode(['status' => 'error', 'message' => 'Conflit de dates.']);
                        exit;
                    }
                }
            }
        }

        $item = ['id' => $id, 'type' => $type, 'date_debut' => $date_debut, 'date_fin' => $date_fin];
        $deja_present = false;
        foreach ($_SESSION['panier'] as $p) {
            if ($p['id'] == $id && $p['type'] == $type) { $deja_present = true; break; }
        }
        if (!$deja_present) { $_SESSION['panier'][] = $item; }

        echo json_encode(['status' => 'success']);
        exit;
    }
}

echo json_encode(['status' => 'error', 'message' => 'Paramètres invalides']);
?>
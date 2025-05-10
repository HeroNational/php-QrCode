<?php
session_start(); // Pour récupérer les données du formulaire

if (isset($_GET['file'])) {
    $file = '../temp/' . basename($_GET['file']);
    
    if (file_exists($file)) {
        // Déterminer le type MIME en fonction de l'extension
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $validExtensions = ['png', 'svg', 'eps'];
        if (!in_array($extension, $validExtensions)) {
            die('Invalid file type.');
        }
        // Création du nom de fichier personnalisé avec les données de session
        $firstname = isset($_SESSION['firstname']) ? $_SESSION['firstname'] : '';
        $lastname = isset($_SESSION['lastname']) ? $_SESSION['lastname'] : '';
        
        // Nettoyer et formater le nom du fichier
        $filename = strtolower(trim($lastname . '_' . $firstname));
        $filename = preg_replace('/[^a-z0-9-_]/', '', $filename); // Enlever les caractères spéciaux
        $filename = 'qr_' . $filename . '-'.mt_rand(100,999)  .'.'. $extension;

        switch ($extension) {
            case 'png':
                $mime = 'image/png';
                break;
            case 'svg':
                $mime = 'image/svg+xml';
                break;
            case 'eps':
                $mime = 'application/postscript';
                break;
            default:
                $mime = 'application/octet-stream';
        }

        // Headers pour forcer le téléchargement
        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . $filename. '"');
        header('Content-Length: ' . filesize($file));
        header('Cache-Control: no-cache');
        
        // Lire et envoyer le fichier
        readfile($file);
        exit;
    }
}
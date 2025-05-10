<?php

require __DIR__ . '/../../vendor/autoload.php';

// Importation des classes nécessaires pour la génération de QR Code
// Classe principale pour créer des QR Codes
use Endroid\QrCode\QrCode;
// Writers pour différents formats de sortie
use Endroid\QrCode\Writer\PngWriter;    // Pour format PNG
use Endroid\QrCode\Writer\SvgWriter;    // Pour format SVG
use Endroid\QrCode\Writer\EpsWriter;    // Pour format EPS
// Niveaux de correction d'erreur disponibles
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;       // 7% de correction
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;    // 15% de correction
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelQuartile;  // 25% de correction
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;      // 30% de correction

/**
 * Script de génération de QR Code pour vCard
 * Ce script traite les données du formulaire et génère un QR Code contenant les informations de contact
 */

// Vérifie si le répertoire temporaire existe, sinon le crée
if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);

// Vérifie si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nettoie toutes les entrées utilisateur pour prévenir les injections XSS
    $_POST['firstname'] = isset($_POST['firstname']) ? cleanInput($_POST['firstname']) : '';
    $_POST['lastname'] = isset($_POST['lastname']) ? cleanInput($_POST['lastname']) : '';
    $_POST['title'] = isset($_POST['title']) ? cleanInput($_POST['title']) : '';
    $_POST['organization'] = isset($_POST['organization']) ? cleanInput($_POST['organization']) : '';
    $_POST['address'] = isset($_POST['address']) ? cleanInput($_POST['address']) : '';
    $_POST['email'] = isset($_POST['email']) ? cleanInput($_POST['email']) : '';
    $_POST['url'] = isset($_POST['url']) ? cleanInput($_POST['url']) : '';
    $_POST['mobile'] = isset($_POST['mobile']) ? cleanInput($_POST['mobile']) : '';
    $_POST['country_code'] = isset($_POST['country_code']) ? cleanInput($_POST['country_code']) : '';

    // Stockage des données dans la session pour une utilisation ultérieure
    $_SESSION['firstname'] = $_POST['firstname'];
    $_SESSION['lastname'] = $_POST['lastname'];
    
    // Configuration du niveau de correction d'erreur
    $errorCorrectionLevel = isset($_REQUEST['level']) ? $_REQUEST['level'] : 'L';
    if (in_array($errorCorrectionLevel, array('L','M','Q','H'))) {
        $errorCorrectionLevel = $_REQUEST['level'];
    }

    // Configuration de la taille de la matrice
    $matrixPointSize = isset($_REQUEST['size']) ? min(max((int)$_REQUEST['size'], 1), 10) : 4;

    // Concaténation du code pays avec le numéro de mobile
    $phone = $_POST['country_code'] . $_POST['mobile'];
    
    /**
     * Construction du contenu vCard
     * Format standard vCard version 2.1
     */
    $content = "BEGIN:VCARD\nVERSION:2.1\n";
    $content .= "N:".$_POST['lastname'].";".$_POST['firstname']."\n";
    $content .= "FN:".$_POST['lastname']." ".$_POST['firstname']."\n";
    
    // Ajout des champs optionnels seulement s'ils sont remplis
    if (!empty($_POST['organization'])) {
        $content .= "ORG:".$_POST['organization']."\n";
    }
    if (!empty($_POST['title'])) {
        $content .= "TITLE:".$_POST['title']."\n";
    }
    if (!empty($_POST['mobile'])) {
        $content .= "TEL;TYPE=cell:".$_POST['country_code'].$_POST['mobile']."\n";
    }
    if (!empty($_POST['address'])) {
        $content .= "ADR;HOME:;;".$_POST['address']."\n";
    }
    if (!empty($_POST['email'])) {
        $content .= "EMAIL:".strtolower($_POST['email'])."\n";
    }
    if (!empty($_POST['url'])) {
        $content .= "URL:".$_POST['url']."\n";
    }
    
    $content .= "END:VCARD";

    // Détermine le format de sortie (png par défaut)
    $format = isset($_REQUEST['format']) ? strtolower($_REQUEST['format']) : 'png';
    $filename = $PNG_TEMP_DIR.'vcard_'.time().'.'.$format;
    
    // Initialisation de l'objet QR Code avec le contenu vCard
    $qrCode = new QrCode($content);
    
    /**
     * Configuration du niveau de correction d'erreur du QR Code
     * Plus le niveau est élevé, plus le QR Code peut être endommagé tout en restant lisible
     */
    switch($errorCorrectionLevel) {
        case 'L': // 7% de correction
            $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevelLow()); 
            break;
        case 'M': // 15% de correction
            $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevelMedium()); 
            break;
        case 'Q': // 25% de correction
            $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevelQuartile()); 
            break;
        case 'H': // 30% de correction
            $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh()); 
            break;
    }

    // Définition de la taille finale du QR Code
    $qrCode->setSize($matrixPointSize * 50); // Multiplié par 50 pour une meilleure visibilité

    /**
     * Sélection du writer approprié selon le format de sortie demandé
     * Supports : PNG, SVG, EPS
     */
    switch($format) {
        case 'svg':
            $writer = new SvgWriter();
            $filename = $PNG_TEMP_DIR.'vcard_'.time().'.svg';
            break;
        case 'eps':
            $writer = new EpsWriter();
            $filename = $PNG_TEMP_DIR.'vcard_'.time().'.eps';
            break;
        default:
            $writer = new PngWriter();
            $filename = $PNG_TEMP_DIR.'vcard_'.time().'.png';
    }

    // Génération et sauvegarde du QR Code
    $result = $writer->write($qrCode);
    $result->saveToFile($filename);
}

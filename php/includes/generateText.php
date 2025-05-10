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
 * Script de génération de QR Code pour texte simple
 * Ce script traite les données du formulaire et génère un QR Code contenant le texte fourni
 */

// Vérifie si le répertoire temporaire existe, sinon le crée
if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);

// Vérifie si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['text'])) {
    // Nettoie l'entrée texte pour prévenir les injections XSS
    $text = cleanInput($_POST['text']);

    // Configuration du niveau de correction d'erreur (L=Low, M=Medium, Q=Quartile, H=High)
    $errorCorrectionLevel = 'L';
    if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H')))
        $errorCorrectionLevel = $_REQUEST['level'];    

    // Configuration de la taille de la matrice (entre 1 et 10)
    $matrixPointSize = 4;
    if (isset($_REQUEST['size']))
        $matrixPointSize = min(max((int)$_REQUEST['size'], 1), 10);

    // Détermine le format de sortie (png par défaut)
    $format = isset($_REQUEST['format']) ? strtolower($_REQUEST['format']) : 'png';
    $filename = $PNG_TEMP_DIR.'text_'.time().'.'.$format;
    
    // Initialisation de l'objet QR Code avec le texte
    $qrCode = new QrCode($text);
    
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
            $filename = $PNG_TEMP_DIR.'text_'.time().'.svg';
            break;
        case 'eps':
            $writer = new EpsWriter();
            $filename = $PNG_TEMP_DIR.'text_'.time().'.eps';
            break;
        default:
            $writer = new PngWriter();
            $filename = $PNG_TEMP_DIR.'text_'.time().'.png';
    }

    // Génération et sauvegarde du QR Code
    $result = $writer->write($qrCode);
    $result->saveToFile($filename);

    // Stockage du texte dans la session pour réaffichage
    $_SESSION['qr_text'] = $text;
}
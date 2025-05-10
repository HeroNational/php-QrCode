<?php

// Inclusion des dépendances via Composer
require __DIR__ . '/../../vendor/autoload.php';

// Import des classes nécessaires pour la génération de QR Code
// La classe QrCode est la classe principale pour la génération
use Endroid\QrCode\QrCode;
// Import des différents writers (générateurs de format de sortie)
use Endroid\QrCode\Writer\PngWriter;    // Pour générer en PNG
use Endroid\QrCode\Writer\SvgWriter;    // Pour générer en SVG
use Endroid\QrCode\Writer\EpsWriter;    // Pour générer en EPS
// Import des niveaux de correction d'erreur disponibles
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;       // Niveau L: 7% de correction
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;    // Niveau M: 15% de correction
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelQuartile;  // Niveau Q: 25% de correction
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;      // Niveau H: 30% de correction

// Création du répertoire temporaire si inexistant
if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);

// Traitement uniquement si on reçoit une requête POST avec un texte
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['text'])) {
    // Sécurisation de l'entrée texte
    $text = cleanInput($_POST['text']);

    // Paramétrage du niveau de correction d'erreur
    // Par défaut: niveau L (Low)
    $errorCorrectionLevel = 'L';
    if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H')))
        $errorCorrectionLevel = $_REQUEST['level'];    

    // Configuration de la taille de la matrice
    // Valeur par défaut: 4, limitée entre 1 et 10
    $matrixPointSize = 4;
    if (isset($_REQUEST['size']))
        $matrixPointSize = min(max((int)$_REQUEST['size'], 1), 10);

    // Détermination du format de sortie (png par défaut)
    $format = isset($_REQUEST['format']) ? strtolower($_REQUEST['format']) : 'png';
    $filename = $PNG_TEMP_DIR.'text_'.time().'.'.$format;
    
    // Création de l'instance QR Code avec le texte fourni
    $qrCode = new QrCode($text);
    
    // Application du niveau de correction d'erreur choisi
    switch($errorCorrectionLevel) {
        case 'L': // Correction faible mais QR Code plus petit
            $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevelLow()); 
            break;
        case 'M': // Équilibre entre correction et taille
            $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevelMedium()); 
            break;
        case 'Q': // Bonne correction, QR Code plus grand
            $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevelQuartile()); 
            break;
        case 'H': // Correction maximale, QR Code le plus grand
            $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh()); 
            break;
    }

    // Définition de la taille du QR Code (en pixels)
    $qrCode->setSize($matrixPointSize * 50); // Facteur 50 pour une taille confortable

    // Sélection et configuration du générateur selon le format souhaité
    switch($format) {
        case 'svg':
            /**
             * Generate SVG QR Code for vCard
             * 
             * @var SvgWriter $writer The SVG writer instance for QR code generation
             * @var string $filename The complete path where the SVG file will be saved
             * @var string $filenameDisplay The display name of the file (same as $filename in this case)
             * 
             * Note: The filename is generated using the temporary directory path,
             * 'vcard_' prefix, current timestamp, and '.svg' extension
             */
            $writer = new SvgWriter();
            $filename = $PNG_TEMP_DIR.'text_'.time().'.svg';
            $filenameDisplay = $filename;
            break;
        case 'eps':
            /**
             * Generates EPS and SVG files for QR Code
             * 
             * @var EpsWriter $writer Instance of EpsWriter for EPS file generation
             * @var string $filename Path and name of the temporary EPS file with timestamp
             * @var SvgWriter $writerDisplay Instance of SvgWriter for SVG preview generation
             * @var string $filenameDisplay Path and name of the temporary SVG file with timestamp
             * 
             * Note: PNG_TEMP_DIR constant must be defined before using this code
             * The SVG file is created for preview purposes while EPS is for final output
             */
            $writer = new EpsWriter();
            $filename = $PNG_TEMP_DIR.'text_'.time().'.eps';
            // Création d'une version SVG pour la prévisualisation
            $writerDisplay = new SvgWriter();
            $filenameDisplay = $PNG_TEMP_DIR.'text_'.time().'.svg';
            break;
        default:
            /**
             * Generate vCard QR code image
             * 
             * @param PngWriter $writer QR code writer instance for PNG format
             * @param string $filename Full path where the QR code image will be saved
             * @param string $filenameDisplay Display name of the generated file
             * 
             * Notes:
             * - Uses PNG_TEMP_DIR constant for temporary directory path
             * - Appends timestamp to filename to ensure uniqueness
             * - Creates PNG format QR code image
             */
            $writer = new PngWriter();
            $filename = $PNG_TEMP_DIR.'text_'.time().'.png';
            $filenameDisplay = $filename;
    }

    // Génération et sauvegarde du QR Code
    $result = $writer->write($qrCode);
    $result->saveToFile($filename);
    
    // Pour le format EPS, génération d'une version SVG pour l'affichage web
    if($format == 'eps') {
        $resultDisplay = $writerDisplay->write($qrCode);
        $resultDisplay->saveToFile($filenameDisplay);
    }
    
    // Mémorisation du texte dans la session pour réutilisation
    $_SESSION['qr_text'] = $text;
}

<?php

// Inclusion des dépendances via Composer
require __DIR__ . '/../../vendor/autoload.php';

// Importation des classes nécessaires pour la génération de QR Code
use Endroid\QrCode\QrCode;               // Classe principale pour générer des QR Codes
use Endroid\QrCode\Writer\PngWriter;     // Writer pour format PNG
use Endroid\QrCode\Writer\SvgWriter;     // Writer pour format SVG
use Endroid\QrCode\Writer\EpsWriter;     // Writer pour format EPS

// Import des différents niveaux de correction d'erreur disponibles
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;       // Niveau faible : 7%
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;    // Niveau moyen : 15%
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelQuartile;  // Niveau élevé : 25%
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;      // Niveau maximum : 30%

// Vérification et création du répertoire temporaire si nécessaire
if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);

// Traitement uniquement si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nettoyage sécurisé des données reçues du formulaire
    $_POST['firstname'] = isset($_POST['firstname']) ? cleanInput($_POST['firstname']) : '';
    $_POST['lastname'] = isset($_POST['lastname']) ? cleanInput($_POST['lastname']) : '';
    $_POST['title'] = isset($_POST['title']) ? cleanInput($_POST['title']) : '';
    $_POST['organization'] = isset($_POST['organization']) ? cleanInput($_POST['organization']) : '';
    $_POST['address'] = isset($_POST['address']) ? cleanInput($_POST['address']) : '';
    $_POST['email'] = isset($_POST['email']) ? cleanInput($_POST['email']) : '';
    $_POST['url'] = isset($_POST['url']) ? cleanInput($_POST['url']) : '';
    $_POST['mobile'] = isset($_POST['mobile']) ? cleanInput($_POST['mobile']) : '';
    $_POST['country_code'] = isset($_POST['country_code']) ? cleanInput($_POST['country_code']) : '';

    // Sauvegarde du nom et prénom dans la session
    $_SESSION['firstname'] = $_POST['firstname'];
    $_SESSION['lastname'] = $_POST['lastname'];
    
    // Configuration du niveau de correction d'erreur (L par défaut)
    $errorCorrectionLevel = isset($_REQUEST['level']) ? $_REQUEST['level'] : 'L';
    if (in_array($errorCorrectionLevel, array('L','M','Q','H'))) {
        $errorCorrectionLevel = $_REQUEST['level'];
    }

    // Configuration de la taille de la matrice (entre 1 et 10)
    $matrixPointSize = isset($_REQUEST['size']) ? min(max((int)$_REQUEST['size'], 1), 10) : 4;

    // Construction du numéro de téléphone complet
    $phone = $_POST['country_code'] . $_POST['mobile'];
    
    // Construction du contenu de la vCard au format 2.1
    $content = "BEGIN:VCARD\nVERSION:2.1\n";
    $content .= "N:".$_POST['lastname'].";".$_POST['firstname']."\n";
    $content .= "FN:".$_POST['lastname']." ".$_POST['firstname']."\n";
    
    // Ajout conditionnel des champs optionnels
    if (!empty($_POST['organization'])) {
        $content .= "ORG:".$_POST['organization']."\n";
    }
    if (!empty($_POST['title'])) {
        $content .= "TITLE:".$_POST['title']."\n";
    }
    if (!empty($_POST['address'])) {
        $content .= "ADR;HOME:".$_POST['address']."\n";
    }
    if (!empty($_POST['email'])) {
        $content .= "EMAIL;PREF;INTERNET:".$_POST['email']."\n";
    }
    if (!empty($_POST['url'])) {
        $content .= "URL:".$_POST['url']."\n";
    }
    if (!empty($_POST['mobile'])) {
        $content .= "TEL;CELL:".$phone."\n";
    }
    
    $content .= "END:VCARD";

    // Détermination du format de sortie
    $format = isset($_REQUEST['format']) ? strtolower($_REQUEST['format']) : 'png';
    $filename = $PNG_TEMP_DIR.'vcard_'.time().'.'.$format;
    
    // Création de l'objet QR Code
    $qrCode = new QrCode($content);
    
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

    // Définition de la taille du QR Code
    $qrCode->setSize($matrixPointSize * 50);

    // Sélection du writer selon le format demandé
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
            $filename = $PNG_TEMP_DIR.'vcard_'.time().'.svg';
            $filenameDisplay = $filename;

            break;
        case 'eps':
            // Pour EPS, on crée aussi une version SVG pour la prévisualisation
            /**
             * QR Code Writer Configuration for vCard
             * 
             * Creates two QR code writers:
             * - EPS Writer for generating EPS format QR code
             * - SVG Writer for display purposes
             * 
             * Generates unique filenames using current timestamp:
             * - EPS file stored in temporary directory with format 'vcard_[timestamp].eps'
             * - SVG file stored in temporary directory with format 'vcard_[timestamp].svg'
             * 
             * @var EpsWriter $writer Writer for EPS format
             * @var string $filename Path to generated EPS file
             * @var SvgWriter $writerDisplay Writer for SVG format
             * @var string $filenameDisplay Path to generated SVG file
             */
            $writer = new EpsWriter();
            $filename = $PNG_TEMP_DIR.'vcard_'.time().'.eps';
            $writerDisplay = new SvgWriter();
            $filenameDisplay = $PNG_TEMP_DIR.'vcard_'.time().'.svg';
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
            $filename = $PNG_TEMP_DIR.'vcard_'.time().'.png';
            $filenameDisplay = $filename;
    }

    // Génération et sauvegarde du QR Code
    $result = $writer->write($qrCode);
    $result->saveToFile($filename);

    // Génération du fichier de prévisualisation pour le format EPS
    if($format == 'eps') {
        $resultDisplay = $writerDisplay->write($qrCode);
        $resultDisplay->saveToFile($filenameDisplay);
    }
}

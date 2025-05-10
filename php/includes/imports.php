<?php

// Correction du chemin vers l'autoloader de Composer
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

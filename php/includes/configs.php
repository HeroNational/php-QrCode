<?php

/**
 * Fichier de configuration pour la génération de codes QR
 *
 * @file configs.php
 * 
 * Ce fichier contient la configuration des couleurs pour la génération de codes QR :
 * - Un tableau de 20 couleurs sombres soigneusement sélectionnées au format hexadécimal
 * - Logique pour sélectionner aléatoirement deux couleurs différentes pour le style du code QR
 * 
 * @var array $colors Tableau de 20 codes de couleurs hexadécimaux
 * @var string $color1 Première couleur sélectionnée aléatoirement
 * @var string $color2 Deuxième couleur sélectionnée aléatoirement (garantie différente de $color1)
 * 
 * @example
 * $colors = ['#4A148C', '#1A237E', ...];
 * $color1 = $colors[array_rand($colors)];
 * $color2 = $colors[array_rand($colors)];
 * 
 * @note Assurez-vous que le répertoire temp existe et a les permissions d'écriture
 * @important Le répertoire temp doit être nettoyé régulièrement pour éviter les problèmes d'espace disque
 */

// Liste de 20 belles couleurs
$colors = [
    '#4A148C', '#1A237E', '#0D47A1', '#01579B', '#006064',
    '#004D40', '#1B5E20', '#33691E', '#827717', '#E65100',
    '#BF360C', '#3E2723', '#263238', '#4527A0', '#283593',
    '#1565C0', '#0277BD', '#00838F', '#00695C', '#2E7D32'
];

// Choisir deux couleurs aléatoires
$color1 = $colors[array_rand($colors)];
$color2 = $colors[array_rand($colors)];
// S'assurer que les deux couleurs sont différentes
while ($color1 === $color2) {
    $color2 = $colors[array_rand($colors)];
}
    
//set it to writable location, a place for temp generated PNG files
$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;

//html PNG location prefix
$File_WEB_DIR = '../../temp/';
    

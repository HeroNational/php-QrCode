<?php

/**
 * Nettoie les données d'entrée en supprimant les points-virgules
 * 
 * Cette fonction de sécurité supprime tous les points-virgules d'une chaîne 
 * pour éviter les injections SQL ou autres attaques basées sur ce caractère.
 *
 * @param string $data La chaîne à nettoyer
 * @return string La chaîne nettoyée sans points-virgules
 */
// Fonction pour nettoyer les entrées
function cleanInput($data) {
    return str_replace(';', '', $data);
}
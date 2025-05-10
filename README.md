# Générateur de QR Code (php-QrCode)

## Description

Application web permettant de générer des QR Codes au format vCard ou texte simple, avec une interface moderne et intuitive.

## Fonctionnalités

- Génération de QR Codes :
  - Format vCard (carte de visite)
  - Format texte simple
- Export en plusieurs formats :
  - PNG (bitmap)
  - SVG (vectoriel)
  - EPS (vectoriel)
- Interface moderne avec dégradés dynamiques
- Personnalisation avancée du QR Code

## Prérequis

- PHP 7.4+
- Composer
- Serveur Web (Apache, Nginx...)
- Extensions PHP :
  - GD Library
  - DOM Extension

## Installation

### Option 1 : Via Composer (recommandée)

```bash
composer require heronational/php-qrcode
```

### Option 2 : Installation manuelle

1. Cloner le dépôt :

```bash
git clone https://github.com/HeroNational/php-QrCode
cd php-QrCode
```

2. Installer les dépendances :

```bash
composer install
```

3. Configurer les permissions du dossier temp :

```bash
chmod 777 temp/
```

> **Note** : L'installation via Composer (Option 1) est recommandée car elle gère automatiquement les dépendances et les mises à jour.

## Structure du Projet

```
qrCode-master/
├── php/
│   └── includes/
│       ├── configs.php      # Configuration
│       ├── functions.php    # Fonctions utilitaires
│       ├── generateText.php # Générateur QR texte
│       ├── generatevCard.php# Générateur QR vCard
│       └── imports.php      # Import des librairies
├── temp/                    # QR codes générés
├── vendor/                  # Dépendances
├── composer.json       
├── index.php               # Point d'entrée
└── README.md
```

## Utilisation

### une vCard

1. Sélectionnez l'onglet "vCard"
2. Remplissez les informations de contact
3. Choisissez le format de sortie (PNG, SVG, EPS)
4. Définissez le niveau de correction et la taille
5. Cliquez sur "Générer"

### un Texte

1. Sélectionnez l'onglet "Texte"
2. Saisissez votre texte
3. Configurez les options de génération
4. Cliquez sur "Générer"

## Options de Correction

| Niveau | Description | Correction |
| ------ | ----------- | ---------- |
| L      | Minimal     | 7%         |
| M      | Standard    | 15%        |
| Q      | Élevé     | 25%        |
| H      | Maximal     | 30%        |

### d'Export

- **PNG** : Format bitmap standard
- **SVG** : Format vectoriel web
- **EPS** : Format vectoriel impression

## Sécurité

- Nettoyage des entrées utilisateur
- Protection XSS
- Validation des formats
- Gestion sécurisée des sessions

## Technologies

- PHP 7.4+
- Bootstrap 5
- jQuery
- Select2
- Endroid/QR-Code

## Contribution

1. Forkez le projet
2. Créez une branche (`git checkout -b feature/AmazingFeature`)
3. Commitez vos changements (`git commit -m 'Add AmazingFeature'`)
4. Poussez la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

## Licence

Distribué sous la licence MIT. Voir `LICENSE` pour plus d'informations.

## Auteur

Jacobin Daniel Fokou - [@Jacobin Daniel Fokou](https://www.linkedin.com/in/jacobindanielfokou)

## Remerciements

- [Endroid/QR-Code](https://github.com/endroid/qr-code)
- [Bootstrap](https://getbootstrap.com)
- [Select2](https://select2.org)

---

*Dernière mise à jour : Mai 2024*

<?php    
    // Démarrer une session PHP pour gérer les données entre les pages
    session_start();

    // Importer les fichiers nécessaires
    require_once __DIR__.'/PHP/includes/imports.php';    // Contient les imports de librairies
    require_once __DIR__.'/PHP/includes/functions.php';  // Contient les fonctions utilitaires
    require_once __DIR__.'/PHP/includes/configs.php';    // Contient les configurations

    // Déterminer le type de QR code à générer (text ou vcard)
    // Si aucun type n'est spécifié dans POST, utiliser 'vcard' par défaut
    $qrType = isset($_POST['qrType']) ? $_POST['qrType'] : 'vcard';

    // Charger le fichier de génération approprié selon le type de QR code
    if ($qrType === 'text') {
        // Si c'est un QR code de type texte, charger le générateur de texte
        require_once __DIR__.'/PHP/includes/generateText.php';
    } else {
        // Sinon charger le générateur de vCard
        require_once __DIR__.'/PHP/includes/generatevCard.php';
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Générateur de QR Code - vCard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css" rel="stylesheet" />
    <link rel="shortcut icon" href="https://placehold.co/96x96/FFFFFF/<?php echo str_replace('#','',$color1); ?>?text=QR"/png" type="image/x-icon">
    <link rel="favicon" href="https://placehold.co/96x96/FFFFFF/<?php echo str_replace('#','',$color1); ?>?text=QR"/png" type="image/x-icon">
    <style>
        body {
            background: linear-gradient(<?php echo mt_rand(0,360); ?>deg, <?php echo $color1; ?>, <?php echo $color2; ?>);
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            overflow: auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 30px;
            width: 80%;
            max-width: 1800px;
        }

        h1, h4 {
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .form-container {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-label {
            color: #eee;
            margin-bottom: 0.5rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.4);
        }

        .form-control, .form-select {
            background: rgba(0, 0, 0, 0.2) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #fff !important;
            border-radius: 7px;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #6c757d !important;
            box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.25) !important;
        }

        .btn-primary {
            background: linear-gradient(<?php echo mt_rand(0,360); ?>deg, <?php echo $color1; ?>, <?php echo $color2; ?>);
            border-radius: 7px;
            border: 2px solid rgba(255, 255, 255, 0.57); /* Ajout d'une bordure transparente */
            color: #333;
            padding: 12px 24px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            color: #fff;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
            color: #fff;
            background: linear-gradient(to left, #64b3f4, #c2e59c);
            filter: opacity(1);
        }

        .qr-preview {
            text-align: center;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            margin-top: 00px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .select2-container--default .select2-selection--single {
            background: rgba(0, 0, 0, 0.2) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #fff !important;
            height: calc(2.25rem + 2px); /* Hauteur standard Bootstrap */
            padding: 0.375rem 0.75rem; /* Padding standard Bootstrap */
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #fff !important;
            line-height: 1.5rem; /* Ajustement pour centrer le texte verticalement */
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: calc(2.25rem + 2px); /* Hauteur standard Bootstrap */
        }

        .phone-input-group {
            display: flex;
            gap: 10px;
        }
        .country-code {
            width: 200px !important;
        }

        /* Styles pour la dropdown Select2 */
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #343a40 !important;
            color: #fff !important;
        }

        .select2-results__option {
            color: #000 !important; /* Couleur du texte par défaut */
        }

        .select2-container--default .select2-results__option[aria-selected="true"] {
            background: linear-gradient(<?php echo mt_rand(0,360); ?>deg, <?php echo $color1; ?>, <?php echo $color2; ?>) !important; /* Couleur de fond sélectionnée */
            color: #fff !important; /* Couleur du texte sélectionné */
        }

        /* Styles pour les boutons de bascule */
        .btn-group-toggle .btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            transition: all 0.3s ease;
        }

        .btn-group-toggle .btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .btn-group-toggle .btn.active {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.4);
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
        }

        /* Styles pour la scrollbar - Compatible Webkit (Chrome, Safari, Edge) */
        ::-webkit-scrollbar {
            width: 12px;
            height: 12px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(45deg, <?php echo $color1; ?>, <?php echo $color2; ?>);
            border-radius: 10px;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(45deg, <?php echo $color2; ?>, <?php echo $color1; ?>);
        }

        /* Styles pour Firefox */
        * {
            scrollbar-width: thin;
            scrollbar-color: <?php echo $color1; ?> rgba(255, 255, 255, 0.1);
        }

        /* Style pour la scrollbar dans les éléments avec défilement */
        .form-container, .select2-results__options {
            scrollbar-width: thin;
            scrollbar-color: <?php echo $color1; ?> rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-light p-5">
    <div class="container py-5">
        <h1 class="text-center mb-4">Générateur de QR Code</h1>
        
        <div class="d-flex justify-content-center mb-4">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-outline-light active">
                    <input type="radio" name="qrType" id="vcard" <?php echo $qrType != 'text'?"checked":"";?>> vCard
                </label>
                <label class="btn btn-outline-light">
                    <input type="radio" name="qrType" id="text"  <?php echo $qrType === 'text'?"checked":"";?>> Texte
                </label>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <!-- Formulaire vCard -->
                <div class="form-container" id="vcardForm">
                    <form action="" method="post">
                        <input type="hidden" name="qrType" value="vcard">
                        <div class="row">
                            <!-- Informations personnelles -->
                            <div class="col-md-6 mb-3">
                                <h4 class="mb-3">Informations personnelles</h4>
                                <div class="mb-3">
                                    <label class="form-label">Nom</label>
                                    <input type="text" class="form-control" name="lastname" required 
                                           value="<?php echo isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname']) : ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Prénom</label>
                                    <input type="text" class="form-control" name="firstname" required
                                           value="<?php echo isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Profession</label>
                                    <input type="text" class="form-control" name="title" placeholder="Ex: Développeur Web"
                                           value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Organisation</label>
                                    <input type="text" class="form-control" name="organization" placeholder="Ex: Entreprise SA"
                                           value="<?php echo isset($_POST['organization']) ? htmlspecialchars($_POST['organization']) : ''; ?>">
                                </div>
                            </div>

                            <!-- Contact -->
                            <div class="col-md-6 mb-3">
                                <h4 class="mb-3">Contact</h4>
                                <div class="mb-3">
                                    <label class="form-label">Adresse</label>
                                    <input type="text" class="form-control" name="address"
                                           value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email"
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Site web personnel</label>
                                    <input type="url" class="form-control" name="url"
                                           value="<?php echo isset($_POST['url']) ? htmlspecialchars($_POST['url']) : ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Téléphone mobile</label>
                                    <div class="phone-input-group">
                                        <select name="country_code" class="form-select country-code" id="country-code" value="<?php echo isset($_POST['country_code']) ? htmlspecialchars($_POST['country_code']) : '+1'; ?>">
                                            <option value="+93" >Afghanistan (+93)</option>
                                            <option value="+27">Afrique du Sud (+27)</option>
                                            <option value="+355">Albanie (+355)</option>
                                            <option value="+213">Algérie (+213)</option>
                                            <option value="+49">Allemagne (+49)</option>
                                            <option value="+376">Andorre (+376)</option>
                                            <option value="+244">Angola (+244)</option>
                                            <option value="+1-268">Antigua-et-Barbuda (+1-268)</option>
                                            <option value="+966">Arabie Saoudite (+966)</option>
                                            <option value="+54">Argentine (+54)</option>
                                            <option value="+374">Arménie (+374)</option>
                                            <option value="+297">Aruba (+297)</option>
                                            <option value="+61">Australie (+61)</option>
                                            <option value="+43">Autriche (+43)</option>
                                            <option value="+994">Azerbaïdjan (+994)</option>
                                            <option value="+1-242">Bahamas (+1-242)</option>
                                            <option value="+973">Bahreïn (+973)</option>
                                            <option value="+880">Bangladesh (+880)</option>
                                            <option value="+1-246">Barbade (+1-246)</option>
                                            <option value="+32">Belgique (+32)</option>
                                            <option value="+501">Belize (+501)</option>
                                            <option value="+229">Bénin (+229)</option>
                                            <option value="+1-441">Bermudes (+1-441)</option>
                                            <option value="+975">Bhoutan (+975)</option>
                                            <option value="+591">Bolivie (+591)</option>
                                            <option value="+387">Bosnie-Herzégovine (+387)</option>
                                            <option value="+267">Botswana (+267)</option>
                                            <option value="+55">Brésil (+55)</option>
                                            <option value="+673">Brunei (+673)</option>
                                            <option value="+359">Bulgarie (+359)</option>
                                            <option value="+226">Burkina Faso (+226)</option>
                                            <option value="+257">Burundi (+257)</option>
                                            <option value="+855">Cambodge (+855)</option>
                                            <option value="+237">Cameroun (+237)</option>
                                            <option value="+1">Canada (+1)</option>
                                            <option value="+238">Cap-Vert (+238)</option>
                                            <option value="+1-345">Îles Caïmans (+1-345)</option>
                                            <option value="+236">République centrafricaine (+236)</option>
                                            <option value="+56">Chili (+56)</option>
                                            <option value="+86">Chine (+86)</option>
                                            <option value="+57">Colombie (+57)</option>
                                            <option value="+269">Comores (+269)</option>
                                            <option value="+242">Congo (+242)</option>
                                            <option value="+243">RD Congo (+243)</option>
                                            <option value="+682">Îles Cook (+682)</option>
                                            <option value="+506">Costa Rica (+506)</option>
                                            <option value="+225">Côte d'Ivoire (+225)</option>
                                            <option value="+385">Croatie (+385)</option>
                                            <option value="+53">Cuba (+53)</option>
                                            <option value="+599">Curaçao (+599)</option>
                                            <option value="+45">Danemark (+45)</option>
                                            <option value="+253">Djibouti (+253)</option>
                                            <option value="+1-767">Dominique (+1-767)</option>
                                            <option value="+20">Égypte (+20)</option>
                                            <option value="+503">Salvador (+503)</option>
                                            <option value="+971">Émirats arabes unis (+971)</option>
                                            <option value="+593">Équateur (+593)</option>
                                            <option value="+291">Érythrée (+291)</option>
                                            <option value="+372">Estonie (+372)</option>
                                            <option value="+268">Eswatini (+268)</option>
                                            <option value="+251">Éthiopie (+251)</option>
                                            <option value="+298">Îles Féroé (+298)</option>
                                            <option value="+679">Fidji (+679)</option>
                                            <option value="+358">Finlande (+358)</option>
                                            <option value="+33">France (+33)</option>
                                            <option value="+241">Gabon (+241)</option>
                                            <option value="+220">Gambie (+220)</option>
                                            <option value="+995">Géorgie (+995)</option>
                                            <option value="+233">Ghana (+233)</option>
                                            <option value="+350">Gibraltar (+350)</option>
                                            <option value="+30">Grèce (+30)</option>
                                            <option value="+1-473">Grenade (+1-473)</option>
                                            <option value="+502">Guatemala (+502)</option>
                                            <option value="+224">Guinée (+224)</option>
                                            <option value="+245">Guinée-Bissau (+245)</option>
                                            <option value="+592">Guyana (+592)</option>
                                            <option value="+509">Haïti (+509)</option>
                                            <option value="+504">Honduras (+504)</option>
                                            <option value="+852">Hong Kong (+852)</option>
                                            <option value="+36">Hongrie (+36)</option>
                                            <option value="+91">Inde (+91)</option>
                                            <option value="+62">Indonésie (+62)</option>
                                            <option value="+98">Iran (+98)</option>
                                            <option value="+964">Irak (+964)</option>
                                            <option value="+353">Irlande (+353)</option>
                                            <option value="+354">Islande (+354)</option>
                                            <option value="+972">Israël (+972)</option>
                                            <option value="+39">Italie (+39)</option>
                                            <option value="+1-876">Jamaïque (+1-876)</option>
                                            <option value="+81">Japon (+81)</option>
                                            <option value="+962">Jordanie (+962)</option>
                                            <option value="+7">Kazakhstan (+7)</option>
                                            <option value="+254">Kenya (+254)</option>
                                            <option value="+686">Kiribati (+686)</option>
                                            <option value="+965">Koweït (+965)</option>
                                            <option value="+996">Kirghizistan (+996)</option>
                                            <option value="+856">Laos (+856)</option>
                                            <option value="+266">Lesotho (+266)</option>
                                            <option value="+371">Lettonie (+371)</option>
                                            <option value="+961">Liban (+961)</option>
                                            <option value="+231">Liberia (+231)</option>
                                            <option value="+218">Libye (+218)</option>
                                            <option value="+423">Liechtenstein (+423)</option>
                                            <option value="+370">Lituanie (+370)</option>
                                            <option value="+352">Luxembourg (+352)</option>
                                            <option value="+853">Macao (+853)</option>
                                            <option value="+389">Macédoine du Nord (+389)</option>
                                            <option value="+261">Madagascar (+261)</option>
                                            <option value="+265">Malawi (+265)</option>
                                            <option value="+60">Malaisie (+60)</option>
                                            <option value="+960">Maldives (+960)</option>
                                            <option value="+223">Mali (+223)</option>
                                            <option value="+356">Malte (+356)</option>
                                            <option value="+692">Îles Marshall (+692)</option>
                                            <option value="+222">Mauritanie (+222)</option>
                                            <option value="+230">Maurice (+230)</option>
                                            <option value="+52">Mexique (+52)</option>
                                            <option value="+691">Micronésie (+691)</option>
                                            <option value="+373">Moldavie (+373)</option>
                                            <option value="+377">Monaco (+377)</option>
                                            <option value="+976">Mongolie (+976)</option>
                                            <option value="+382">Monténégro (+382)</option>
                                            <option value="+1-664">Montserrat (+1-664)</option>
                                            <option value="+212">Maroc (+212)</option>
                                            <option value="+258">Mozambique (+258)</option>
                                            <option value="+95">Myanmar (+95)</option>
                                            <option value="+264">Namibie (+264)</option>
                                            <option value="+674">Nauru (+674)</option>
                                            <option value="+977">Népal (+977)</option>
                                            <option value="+505">Nicaragua (+505)</option>
                                            <option value="+227">Niger (+227)</option>
                                            <option value="+234">Nigeria (+234)</option>
                                            <option value="+683">Niue (+683)</option>
                                            <option value="+850">Corée du Nord (+850)</option>
                                            <option value="+47">Norvège (+47)</option>
                                            <option value="+64">Nouvelle-Zélande (+64)</option>
                                            <option value="+968">Oman (+968)</option>
                                            <option value="+92">Pakistan (+92)</option>
                                            <option value="+680">Palaos (+680)</option>
                                            <option value="+970">Palestine (+970)</option>
                                            <option value="+507">Panama (+507)</option>
                                            <option value="+675">Papouasie-Nouvelle-Guinée (+675)</option>
                                            <option value="+595">Paraguay (+595)</option>
                                            <option value="+51">Pérou (+51)</option>
                                            <option value="+63">Philippines (+63)</option>
                                            <option value="+48">Pologne (+48)</option>
                                            <option value="+351">Portugal (+351)</option>
                                            <option value="+1-787, +1-939">Porto Rico (+1-787, +1-939)</option>
                                            <option value="+974">Qatar (+974)</option>
                                            <option value="+262">Réunion (+262)</option>
                                            <option value="+40">Roumanie (+40)</option>
                                            <option value="+7">Russie (+7)</option>
                                            <option value="+250">Rwanda (+250)</option>
                                            <option value="+590">Saint-Barthélemy (+590)</option>
                                            <option value="+290">Sainte-Hélène (+290)</option>
                                            <option value="+1-869">Saint-Kitts-et-Nevis (+1-869)</option>
                                            <option value="+1-758">Sainte-Lucie (+1-758)</option>
                                            <option value="+590">Saint-Martin (+590)</option>
                                            <option value="+508">Saint-Pierre-et-Miquelon (+508)</option>
                                            <option value="+1-784">Saint-Vincent-et-les-Grenadines (+1-784)</option>
                                            <option value="+685">Samoa (+685)</option>
                                            <option value="+378">Saint-Marin (+378)</option>
                                            <option value="+239">Sao Tomé-et-Principe (+239)</option>
                                            <option value="+966">Arabie Saoudite (+966)</option>
                                            <option value="+221">Sénégal (+221)</option>
                                            <option value="+381">Serbie (+381)</option>
                                            <option value="+248">Seychelles (+248)</option>
                                            <option value="+232">Sierra Leone (+232)</option>
                                            <option value="+65">Singapour (+65)</option>
                                            <option value="+1-721">Saint-Martin (partie néerlandaise) (+1-721)</option>
                                            <option value="+421">Slovaquie (+421)</option>
                                            <option value="+386">Slovénie (+386)</option>
                                            <option value="+677">Îles Salomon (+677)</option>
                                            <option value="+252">Somalie (+252)</option>
                                            <option value="+27">Afrique du Sud (+27)</option>
                                            <option value="+82">Corée du Sud (+82)</option>
                                            <option value="+211">Soudan du Sud (+211)</option>
                                            <option value="+34">Espagne (+34)</option>
                                            <option value="+94">Sri Lanka (+94)</option>
                                            <option value="+249">Soudan (+249)</option>
                                            <option value="+597">Suriname (+597)</option>
                                            <option value="+46">Suède (+46)</option>
                                            <option value="+41">Suisse (+41)</option>
                                            <option value="+963">Syrie (+963)</option>
                                            <option value="+886">Taïwan (+886)</option>
                                            <option value="+992">Tadjikistan (+992)</option>
                                            <option value="+255">Tanzanie (+255)</option>
                                            <option value="+66">Thaïlande (+66)</option>
                                            <option value="+228">Togo (+228)</option>
                                            <option value="+690">Tokelau (+690)</option>
                                            <option value="+676">Tonga (+676)</option>
                                            <option value="+1-868">Trinité-et-Tobago (+1-868)</option>
                                            <option value="+216">Tunisie (+216)</option>
                                            <option value="+90">Turquie (+90)</option>
                                            <option value="+993">Turkménistan (+993)</option>
                                            <option value="+1-649">Îles Turques-et-Caïques (+1-649)</option>
                                            <option value="+688">Tuvalu (+688)</option>
                                            <option value="+256">Ouganda (+256)</option>
                                            <option value="+380">Ukraine (+380)</option>
                                            <option value="+598">Uruguay (+598)</option>
                                            <option value="+998">Ouzbékistan (+998)</option>
                                            <option value="+678">Vanuatu (+678)</option>
                                            <option value="+379">Vatican (+379)</option>
                                            <option value="+58">Venezuela (+58)</option>
                                            <option value="+84">Vietnam (+84)</option>
                                            <option value="+681">Wallis et Futuna (+681)</option>
                                            <option value="+967">Yémen (+967)</option>
                                            <option value="+260">Zambie (+260)</option>
                                            <option value="+263">Zimbabwe (+263)</option>
                                        </select>
                                        <input type="tel" class="form-control" name="mobile" placeholder="Numéro sans le 0" value="<?php echo isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : ''; ?>" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Paramètres QR Code -->
                            <div class="col-md-12">
                                <h4 class="mb-3">Paramètres du QR Code</h4>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Format de sortie</label>
                                        <select name="format" class="form-select">
                                            <option value="png">PNG</option>
                                            <option value="eps">EPS</option>
                                            <option selected value="svg">SVG</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Niveau de correction</label>
                                        <select name="level" class="form-select">
                                            <option value="L">L - minimal</option>
                                            <option value="M">M - standard</option>
                                            <option value="Q">Q - élevé</option>
                                            <option selected value="H">H - maximal</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Taille</label>
                                        <select name="size" class="form-select">
                                            <?php for($i=1;$i<=10;$i++): ?>
                                                <option <?php echo $i==10? "selected" : "";?> value="<?php echo $i ?>"><?php echo $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-warning btn-lg px-5">Générer le QR Code</button>
                        </div>
                    </form>
                </div>

                <!-- Formulaire Texte -->
                <div class="form-container" id="textForm" style="display: none;">
                    <form action="" method="post">
                        <input type="hidden" name="qrType" value="text">
                        <div class="mb-4">
                            <label class="form-label">Votre texte</label>
                            <textarea class="form-control" name="text" rows="6" placeholder="Saisissez votre texte ici..."><?php echo isset($_POST['text']) ? htmlspecialchars($_POST['text']) : ''; ?></textarea>
                        </div>

                        <!-- Paramètres QR Code -->
                        <div class="col-md-12">
                            <h4 class="mb-3">Paramètres du QR Code</h4>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Format de sortie</label>
                                    <select name="format" class="form-select">
                                        <option value="png">PNG</option>
                                        <option value="eps">EPS</option>
                                        <option selected value="svg">SVG</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Niveau de correction</label>
                                    <select name="level" class="form-select">
                                        <option value="L">L - minimal</option>
                                        <option value="M">M - standard</option>
                                        <option value="Q">Q - élevé</option>
                                        <option selected value="H">H - maximal</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Taille</label>
                                    <select name="size" class="form-select">
                                        <?php for($i=1;$i<=10;$i++): ?>
                                            <option <?php echo $i==10? "selected" : "";?> value="<?php echo $i ?>"><?php echo $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-warning btn-lg px-5">Générer le QR Code</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-4">
                <?php if(isset($filename) && file_exists($filename)): ?>
                    <div class="qr-preview">
                        <h4 class="mb-3">Votre QR Code</h4>
                        <img src="<?php echo isset($filenameDisplay) && file_exists($filenameDisplay)? $File_WEB_DIR.basename($filenameDisplay):"https://placehold.co/500x500/FFFFFF/".str_replace('#','',$color1)."?text=QR"; ?>" class="img-fluid rounded" alt="QR Code généré">
                        <div class="mt-3">
                            <a href="./php/download.php?file=<?php echo basename($filename); ?>" class="btn btn-warning btn-lg">
                                Télécharger le QR Code
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="qr-preview placeholder-container">
                        <h4 class="mb-3">QR Code</h4>
                        <p>Générez votre QR Code au préalable</p>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($content)): ?>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h4>Contenu vCard généré:</h4>
                        <pre><code class="form-control"><?php echo htmlspecialchars($content); ?></code></pre>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-vcard.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialisation de Select2 pour le pays
            $('#country-code').select2({
                placeholder: 'Sélectionner un pays',
                allowClear: true,
                templateResult: formatCountry,
                templateSelection: formatCountry
            });

            // Afficher le formulaire approprié au chargement
            const qrType = '<?php echo $qrType; ?>';
            if (qrType === 'text') {
                $('#vcardForm').hide();
                $('#textForm').show();
                $('#text').prop('checked', true);
                $('#text').closest('.btn').addClass('active');
                $('#vcard').closest('.btn').removeClass('active');
            } else {
                $('#vcardForm').show();
                $('#textForm').hide();
                $('#vcard').prop('checked', true);
                $('#vcard').closest('.btn').addClass('active');
                $('#text').closest('.btn').removeClass('active');
            }

            // Gestion de la bascule entre les formulaires
            $('input[name="qrType"]').on('change', function() {
                if (this.id === 'vcard') {
                    $('#vcardForm').show();
                    $('#textForm').hide();
                } else {
                    $('#vcardForm').hide();
                    $('#textForm').show();
                }
                
                // Mettre à jour la classe active des boutons
                $('.btn-group-toggle .btn').removeClass('active');
                $(this).closest('.btn').addClass('active');
            });
        });

        function formatCountry(country) {
            if (!country.id) return country.text;
            return $(`<span>${country.text}</span>`);
        }

        // Validation du numéro de téléphone
        $('input[name="mobile"]').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Définir la Cameroun comme pays par défaut
        <?php $selectedCode = isset($_POST['country_code']) ? $_POST['country_code'] : '+237'; ?>
        $('#country-code').val('<?php echo $selectedCode; ?>').trigger('change');
    </script>
</body>
</html>


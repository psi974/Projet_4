$(document).ready(function() {
    // Récupération des « data-prototype ».
    var $container = $('#mdl_billetteriebundle_commande_billets');
    // Indicateurs d'erreur de saisie nom/prenom
    var errNom = true;
    var errPrenom = true;
    //--------------------------------------------
    //---Gestion Journée + Heure courante > 14h---
    //--------------------------------------------
    var $infoBilletJ = $('#mdl_billetteriebundle_commande_billetJour');
    // Formatage de la date du jour dd/mm/yyyy
    var d = new Date();
    var day = d.getDate();
    var month = d.getMonth() + 1;
    var year = d.getFullYear();
    if (day < 10) {
        day = "0" + day;
    }
    if (month < 10) {
        month = "0" + month;
    }
    var $dtJour = day + "/" + month + "/" + year;
    // Récupération de l'heure courante
    var $hhActuelle=d.getHours();
    // pour test
    // $hhActuelle = 15;
    $('form').change(function(e) {
        var $dtSel = $('#mdl_billetteriebundle_commande_dtVisite').val();
        if ($dtSel == $dtJour && $hhActuelle > 14)
        {
            $($infoBilletJ).attr("disabled",true);
            $($infoBilletJ).attr('checked',false);
        }else
        {
            $($infoBilletJ).attr("disabled",false);
        }
    });
    //---------------------------------
    //--Gestion des ajouts de billets--
    //---------------------------------
    // Définition du compteur unique pour nommer les champs ajouté dynamiquement
    var index = $container.find(':input').length;
    // Ajout nouveau billet / Clic d'ajout.
    var $addBillet =  $('<a href="#" id="add_billet" class="btn btn-info">Ajouter un billet</a>');
    $addBillet.insertBefore('.commander');
    $addBillet.click(function (e) {
        if (!$addBillet.attr('disabled')) {
            addBillet($container);
            e.preventDefault();
            return false;
        }
        $('.nom:last').focus();
    });
    $('.commander').addClass('pull-right');
    // Ajout automatique du premier champs
    if (index == 0) {
        addBillet($container);
    } else {
        // Ajout du lien de suppression sur les billets existants
        $container.children('div').each(function () {
            addDeleteLink($(this));
        });
    }

    // Fonction d'ajout formulaire BilletType
    function addBillet($container) {
        // Modification du contenu « data-prototype »:
        // 'Billets n°+(index+1)' remplace "__name__label__"
        // index remplace "__name__"
        // Utilisation du regex /x/g pour rechercher dans "data-prototype"
        var template = $container.attr('data-prototype')
            .replace(/__name__label__/g, 'Billet n°' + (index + 1))
            .replace(/__name__/g, index);
        // Création de l'objet JQ pour le template
        var $prototype = $(template);
        // Lien de suppression du billet (sauf billet 1)
        if (index > 0) {
            addDeleteLink($prototype);
        }
        // Ajout du nouveau prototype
        $container.append($prototype);
        // Gestion validation des données saisies
        $('.commander').attr('disabled', true);
        $('#add_billet').attr('disabled', true);
        errNom = true;
        errPrenom = true;

        ///------------------------------------
        //----DatePicker--Date de naissance---
        //------------------------------------
        $('.dtNaiss').datepicker({
            format: "dd/mm/yyyy",
            autoclose: true,
            language: 'fr',
            pickerPosition: "bottom-left",
            startDate: '01/01/1902',
            endDate: 'today'
        });
        // Création de l'objet JQ pour la date de naissaince.
        var $dateNaiss = $('#mdl_billetteriebundle_commande_billets_'+ index +'_dtNaissance');
        $dateNaiss.datepicker('setDate', '01/01/1980');
        $prototype.append('<h4 class="affPrix text-center well">Prix : 16 euros</h4>');
        $('.affPrix:last').attr('id' , 'mdl_billetteriebundle_commande_billets_'+ index +'_prixBillet');
        // Incrémentation du compteur d'ajout
        index++;
    }
    //------------------------------------
    //---Fonction suppression de billet---
    //------------------------------------
    function addDeleteLink($prototype) {
        // Création du lien
        var $deleteLink = $('<a href="#" class="suppr btn btn-danger">Supprimer</a>');
        // Ajout du lien
        $prototype.append($deleteLink);
        // Ajout du listener sur le clic du lien pour supprimer le billet
        $deleteLink.click(function (e) {
            // Suppression billet
            $prototype.remove();
            $('.nom:last').focus();
            $('.commander').attr('disabled', false);
            $('#add_billet').attr('disabled', false);
            e.preventDefault();
            return false;
        });
    }
    //-------------------------------
    //---Gestion Date de naissance---
    //-------------------------------
    $container.on('change', '.dtNaiss', function(e) {
        // Récupération ID du checkbox tarif réduit concerné
        var $reducId = $("#" + $(this).attr('id').replace('dtNaissance','tarifReduit'));
        // Désactivation tarif réduit, si modification date de naissance
        $reducId.prop('checked', false);
        // Récupération ID date de naissance correspondante
        var $dtNId = $("#" + $(this).attr('id'));
        // Calcul age du visiteur (pour interdiction tarif réduit si besoin)
        var dateDuJour = new Date();
        var dateNaissance = $dtNId.datepicker('getDate');
        var age = dateDuJour.getFullYear() - dateNaissance.getFullYear();
        if (dateDuJour.getMonth() < (dateNaissance.getMonth())) {
            age--;
        } else if ((dateNaissance.getMonth() == dateDuJour.getMonth()) &&
            (dateDuJour.getDate() < dateNaissance.getDate())) {
            age--;
        }
        // Récupération ID de l'affichage tarif du billet
        var $prixBilletId = $("#" + $(this).attr('id').replace('dtNaissance', 'prixBillet'));
        // Affichage du prix du billet
        if (age < 4)
        {
            $prixBilletId.text('Prix : 0 €');
        }else if (age >= 4 && age < 12) {
            $prixBilletId.text('Prix : 8 euros')
        }else if (age >= 12 && age < 60) {
            $prixBilletId.text('Prix : 16 euros');
        } else {
            $prixBilletId.text('Prix : 12 euros');
        }

    });
    //-------------------------
    //---Gestion tarif réduit--
    //-------------------------
    $container.on('click', '.reduction', function(e) {
        $('.msgReduc').remove();
        // Message d'erreur
        var $msg = $('<div class="alert alert-block alert-danger msgReduc" style="display:none"><h4 class="textMsg"></h4></div>');
        // Récupération ID de l'affichage tarif du billet
        var $prixBilletId = $("#" + $(this).attr('id').replace('tarifReduit', 'prixBillet'));
        // Récupération ID du checkbox tarif réduit concerné
        var $reducId = $("#" + $(this).attr('id'));
        // Récupération ID date de naissance correspondante
        var $dtNId = $("#" + $(this).attr('id').replace('tarifReduit', 'dtNaissance'));
        // Calcul age du visiteur (pour interdiction tarif réduit si besoin)
        var dateDuJour = new Date();
        var dateNaissance = $dtNId.datepicker('getDate');
        var age = dateDuJour.getFullYear() - dateNaissance.getFullYear();
        if (dateDuJour.getMonth() < (dateNaissance.getMonth()))
        {
            age--;
        } else if((dateNaissance.getMonth() == dateDuJour.getMonth()) &&
            (dateDuJour.getDate() < dateNaissance.getDate()))
        {
            age--;
        }
        // Affichage du prix du billet si tarif réduit décoché
        if (age < 4)
        {
            $prixBilletId.text('Prix : 0 €');
        }else if (age >= 4 && age < 12) {
            $prixBilletId.text('Prix : 8 euros')
        }else if (age >= 12 && age < 60) {
            $prixBilletId.text('Prix : 16 euros');
        } else {
            $prixBilletId.text('Prix : 12 euros');
        }
        if (age < 12) {
            $reducId.before($msg);
            $('.textMsg').text('L\'age du titulaire du billet ne permet pas de sélectionner le tarif réduit');
            $('.msgReduc').addClass('has-danger').fadeIn(1000).delay(4000).fadeOut(2000);
            $reducId.prop('checked', false);
        } else
        {
            var check = $reducId.prop('checked');
            if(check)
            {
                $reducId.before($msg);
                $('.textMsg').text('Attention, un justificatif vous sera demandé à l\'entrée du musée');
                $('.msgReduc').addClass('has-danger').fadeIn(1000).delay(4000).fadeOut(2000);
                $prixBilletId.text('Prix : 10 euros');
            }
        }
    });

    //------------------------------------------
    //---Gestion affichage du prix du billet----
    //------------------------------------------
    $container.on('change', '.dtNaiss', function(e) {
        // Récupération ID de l'affichage tarif du billet
        var $prixBilletId = $("#" + $(this).attr('id').replace('dtNaissance', 'prixBillet'));
        // Récupération ID date de naissance correspondante
        var $dtNId = $("#" + $(this).attr('id'));
        // Récupération ID tarif reduit correspondant
        var $reducId = $("#" + $(this).attr('id').replace('dtNaissance', 'tarifReduit'));
        // Calcul age du visiteur (pour interdiction tarif réduit si besoin)
        var dateDuJour = new Date();
        var dateNaissance = $dtNId.datepicker('getDate');
        var age = dateDuJour.getFullYear() - dateNaissance.getFullYear();
        if (dateDuJour.getMonth() < (dateNaissance.getMonth())) {
            age--;
        } else if ((dateNaissance.getMonth() == dateDuJour.getMonth()) &&
            (dateDuJour.getDate() < dateNaissance.getDate())) {
            age--;
        }
        if (age < 4)
        {
            $prixBilletId.text('Prix : 0 euros');
        }else if (age >= 4 && age < 12) {
            $prixBilletId.text('Prix : 8 euros')
        }else if (age >= 12 && age < 60) {
            $prixBilletId.text('Prix : 16 euros');
        } else {
            $prixBilletId.text('Prix : 12 euros');
        }
    });

    //---------------------------------
    //---Message CTRL zones de saisie--
    //---------------------------------
    $container.click(function (e) {
        if (!errNom && !errPrenom)
        {
            $('.commander').attr('disabled', false);
            $addBillet.attr('disabled', false);
        } else
        {
            $('.commander').attr('disabled', true);
            $addBillet.attr('disabled', true);
        }
    });
    // CTRL Zone de saisie du nom
    //----------------------------
    $container.on('focus' , '.nom' , function(e) {
        // Suppression du message d'erreur sur la zone (si existant)
        var $msgNomIds = $(".msg" + $(this).attr('id'));
        if ($msgNomIds.length) {
            $msgNomIds.remove();
        }
    });
    $container.on('blur', '.nom' , function(e) {
        // Récupération ID du nom concerné
        var $nomId = $("#" + $(this).attr('id'));
        if(!$($nomId).val().match(/^[a-zA-ZàâéèêôùûçïëÀÂÉÈÔÙÛÏË-]{2,20}$/))
        {
            //Construction de la class du msg d'erreur
            var $msgNomIdc = ("msg" + $(this).attr('id'));
            // Construction du message d'erreur
            var $msg = $('<div class="alert alert-block alert-danger msgNom" style="display:none"><h4 class="textMsgNom"></h4></div>');
            $nomId.before($msg);
            $('.textMsgNom').text('Ce nom n\'est pas valide -> Saisir un minimum de 2 caractères et utiliser \'-\' pour les espaces');
            $('.msgNom').addClass('has-danger').fadeIn(1000);
            $('.msgNom').addClass($msgNomIdc);
            errNom = true;
            $('.commander').attr('disabled', true);
            $addBillet.attr('disabled', true);
        }else
        {
            errNom = false;
            // Réactivation des boutons si plus d'erreur de saisie
            //-------------------------------------------------------------------------
            if (!errNom && !errPrenom)
            {
                $('.commander').attr('disabled', false);
                $addBillet.attr('disabled', false);
            }
        }
    });
    // CTRL Zone de saisie du prénom
    //-------------------------------
    $container.on('focus' , '.prenom' , function(e) {
        // Suppression du message d'erreur sur la zone (si existant)
        var $msgPreNomIds = $(".msg" + $(this).attr('id'));
        if ($msgPreNomIds.length) {
            $msgPreNomIds.remove();
        }
    });
    $container.on('blur', '.prenom' , function(e) {
        // Récupération ID du prenom concerné
        var $preNomId = $("#" + $(this).attr('id'));
        if(!$($preNomId).val().match(/^[a-zA-ZàâéèêôùûçïëÀÂÉÈÔÙÛÏË-]{2,20}$/))
        {
            //Construction de la class du msg d'erreur
            var $msgPreNomIdc = ("msg" + $(this).attr('id'));
            // Construction du message d'erreur
            var $msg = $('<div class="alert alert-block alert-danger msgPreNom" style="display:none"><h4 class="textMsgPreNom"></h4></div>');
            $preNomId.before($msg);
            $('.textMsgPreNom').text('Ce prénom n\'est pas valide -> Saisir un minimum de 2 caractères et utiliser \'-\' pour les espaces');
            $('.msgPreNom').addClass('has-danger').fadeIn(1000);
            $('.msgPreNom').addClass($msgPreNomIdc);
            errPrenom = true;
            $('.commander').attr('disabled', true);
            $addBillet.attr('disabled', true);
        }else
        {
            errPrenom = false;
            // Réactivation des boutons si plus d'erreur de saisie
            //-------------------------------------------------------------------------
            if (!errNom && !errPrenom)
            {
                $('.commander').attr('disabled', false);
                $addBillet.attr('disabled', false);
            }
        }
    });
    //---------------------------------
    //----DatePicker--Date de visite---
    //---------------------------------
    var date = new Date();
    var year = date.getFullYear();
    var yearN = year+1;
    var mois = date.getMonth();
    var jour = date.getDate();
    var datedebfr = jour+'-'+(mois+1)+'-'+year;

    $('.js-datepicker').datepicker({
        format: "dd/mm/yyyy",
        autoclose: true,
        language: 'fr',
        pickerPosition: "bottom-left",
        startDate: datedebfr,
        endDate: '31-12-'+yearN,
        daysOfWeekDisabled: "0,2",
        datesDisabled: [
            '01/05/'+year,
            '01/11/'+year,
            '25/12/'+year,
            '01/05/'+yearN,
            '01/11/'+yearN,
            '25/12/'+yearN,
        ]
    });
    $('.js-datepicker').datepicker('setDate', 'today');
});

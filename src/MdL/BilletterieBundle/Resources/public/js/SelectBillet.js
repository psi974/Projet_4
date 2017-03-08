$(document).ready(function() {
    // Récupération des « data-prototype ».
    var $container = $('#mdl_billetteriebundle_commande_billets');

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
    // Ajout nouveau champ / Clic d'ajout.
    $('#add_billet').click(function (e) {
        addBillet($container);
        e.preventDefault();
        return false;
    });

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

        // Incrémentation du compteur d'ajout
        index++;
    }
    //------------------------------------
    //---Fonction suppression de billet---
    //------------------------------------
    function addDeleteLink($prototype) {
        // Création du lien
        var $deleteLink = $('<a href="#" class="btn btn-danger">Supprimer</a>');
        // Ajout du lien
        $prototype.append($deleteLink);

        // Ajout du listener sur le clic du lien pour supprimer le billet
        $deleteLink.click(function (e) {
            $prototype.remove();
            e.preventDefault();
            return false;
        });
    }
    //-------------------------------------
    //---Message information tarif réduit--
    //-------------------------------------
    $($container).on('click', '.reduction', function(e) {
        // Récupération ID du checkbox concerné
        var $reducId = $("#"+$(this).attr('id'));
        $('.alert-danger').remove();
        var $msg = $('<div class="alert alert-block alert-danger" style="display:none"><h4 class="textMsg"></h4></div>');
        var check = $($reducId).prop("checked");
        if(check)
        {
            $reducId.before($msg);
            $('.textMsg').text('Attention, un justificatif vous sera demandé à l\'entrée du musée');
            $('.alert-danger').addClass('has-danger').fadeIn(1000).delay(4000).fadeOut(2000);
        }
    });

    //----------------
    // ---DatePicker--
    //-----------------
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

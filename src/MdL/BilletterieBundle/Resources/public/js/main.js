$(document).ready(function() {
    // Date picker
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
        endDate: '31-12-' + yearN,
        daysOfWeekDisabled: "0,2",
        datesDisabled: [
            '01/05/' + year,
            '01/11/' + year,
            '25/12/' + year,
            '01/05/' + yearN,
            '01/11/' + yearN,
            '25/12/' + yearN,
        ],
    });
    var dtvst = $('.datepicker').val();
    console.log(dtvst);
    // Total du nombre de billets du jour sélectionné //
    //function ajaxCall(commande_dtVisite)
    //{
    //    var dt
    //    $.ajax(
    //        {
    //            url: "{{path('count_billet')}}",
    //            method: "post",
    //            data: 'dtv'
    //        });
    //}

});
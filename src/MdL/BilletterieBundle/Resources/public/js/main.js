$(document).ready(function() {

    // Date picker
    var date = new Date();
    date.setDate(date.getDate());

    $('.js-datepicker').datepicker({
        format: "dd/mm/yyyy",
        autoclose: true,
        language: 'fr',
        pickerPosition: "bottom-left",
        startDate: date,
        endDate: '2017-12-31',
        daysOfWeekDisabled: [2,6],
        datesDisabled: [
            new Date(2017, 12 - 1, 25),
            new Date(2017, 5 - 1, 2)
        ]
    });
});
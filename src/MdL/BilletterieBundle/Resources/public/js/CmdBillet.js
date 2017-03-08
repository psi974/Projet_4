$(document).ready(function() {
    // Gestion du bouton stripe "Régler votre commande"
    $('.stripe-button-el span').text('Régler votre commande');
    $('.stripe-button-el span').css({
        color: 'rgb(222,222,222)',
        fontFamily: 'Bitter',
        fontWeight: 100
    });
    $('.stripe-button-el').mouseout(function()
        {
            $('.stripe-button-el span').css({
                color: 'rgb(222,222,222)',
                fontFamily: 'Bitter',
                fontWeight: 100
            });
        }
    );

    $('.stripe-button-el').mouseover(function()
        {
            $('.stripe-button-el span').css({
                color: 'white',
                fontWeight: 'bolder'
            })
        }
    );
});

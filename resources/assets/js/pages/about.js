function addEventListeners() {

    var tabNav = $('#about-tab-nav');
    tabNav.find('li a').click(function(){

        var tab = $(this).html();
        var link = tab.replace(' ', '-').toLowerCase();
        $.urlParam('tab', link);

    });

}

function determineTab() {

    let tab = $.urlParam('tab');

    if (tab) {

        $('#about-tab-nav').find('.uk-active').removeClass('uk-active');
        $('.tab-' + tab).addClass('uk-active');

    }

}

$(window).ready(function(){

    determineTab();
    addEventListeners();

});
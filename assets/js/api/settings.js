(function($) {

    //var server_request = $.get('https://7srummy.com/7s-rummy-rest-api/settings/get-settings');

    var server_request = $.get('http://localhost:81/7srummy/7s-rummy-rest-api/settings/get-settings');

    server_request.done(function (data) {

        let obj = data.data.settings[0];
        var logo='';
        var banner_title='';
        var footer_logo='';
        var footer='';

        logo = logo + '<a href="index"><img src="'+obj.logo_image+'" alt="logo"></a>';
        $("#logo").append(logo);

        footer_logo = footer_logo + ' <a href="index"><img src="'+obj.logo_image+'" alt="logo" style="width: 84px;height: 55px;"></a>';
        $("#footer_logo").append(footer_logo);

        banner_title = banner_title + '<h4 class="dd1">'+obj.banner_title+'</h4>';
        $("#banner_title").append(banner_title);

        footer = footer + '<div class="navbar-brand">'+obj.footer+'</div>';
        $("#copyright").append(footer);

    });
    
})(jQuery);





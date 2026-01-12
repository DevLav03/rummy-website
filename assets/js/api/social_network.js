(function($) {

    //var server_request = $.get('https://7srummy.com/7s-rummy-rest-api/social-media/get-social-medias');

    var server_request = $.get('http://localhost:81/7srummy/7s-rummy-rest-api/social-media/get-social-medias');

    server_request.done(function (data) {

        let obj = data.data.socialmedia[0];
        var faqHtml='';


        faqHtml = faqHtml + '<table><tbody><tr><td><a class="nav" id="social-media-google" href="'+obj.google+'" target="_blank"><img src="assets/images/footer/social-media/google.svg"></a></td><td><a class="nav" id="social-media-facebook" href="'+obj.facebook+'" target="_blank"><img src="assets/images/footer/social-media/facebook.svg"></a></td><td><a class="nav" id="social-media-google_paly" href="'+obj.playstore+'" target="_blank"><img src="assets/images/footer/social-media/play_store.svg"></a></td><td><a class="nav" id="social-media-andriod" href="'+obj.android+'" target="_blank"><img src="assets/images/footer/social-media/andriod.svg"></a></td><td><a class="nav" id="social-media-iOS" href="'+obj.ios+'" target="_blank"><img src="assets/images/footer/social-media/ios.svg"></a></td></tbody></table>';
        
        $("#social_media_link").append(faqHtml);
    });
    
})(jQuery);





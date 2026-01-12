(function($) {

    //var server_request = $.get('https://7srummy.com/7s-rummy-rest-api/faq/get-faq');

    var server_request = $.get('http://localhost:81/7srummy/7s-rummy-rest-api/faq/get-faq');
   
    server_request.done(function (data) {
        
        let obj = data.data.faq;
        var faqHtml='';

        for (let i = 0; i < obj.length; i++) {
            faqHtml = faqHtml + '<div class="row"><div class="col-md-12"><div id="tabs"><div class="tab"><input type="checkbox" id="faq'+obj[i].id+'"><label class="tab-label" for=faq'+obj[i].id+'>'+obj[i].title+'</label><div class="tab-content">'+obj[i].answer+'</div><div class="box-style3 b-t-s2"></div></div></div></div> </div>';
        }

        $("#faq_post").append(faqHtml);

    });
    

})(jQuery);

(function($) {

   
    //var server_request = $.get('https://7srummy.com/7s-rummy-rest-api/faq/get-latest-faq');

    var server_request = $.get('http://localhost:81/7srummy/7s-rummy-rest-api/faq/get-latest-faq');
   
    server_request.done(function (data) {
        
        let obj = data.data.faq;
        var faqHtml='';

        for (let i = 0; i < obj.length; i++) {
            faqHtml = faqHtml + '<div class="row"><div class="col-md-12"><div id="tabs"><div class="tab"><input type="checkbox" id="faq'+obj[i].id+'"><label class="tab-label" for=faq'+obj[i].id+'>'+obj[i].title+'</label><div class="tab-content">'+obj[i].answer+'</div><div class="box-style3 b-t-s2"></div></div></div></div> </div>';
        }

        $("#faq_latest").append(faqHtml);
        
    });
    

})(jQuery);

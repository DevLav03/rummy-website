(function($) {

    //var server_request = $.get('https://7srummy.com/7s-rummy-rest-api/news/get-latest-news');

    var server_request = $.get('http://localhost:81/7srummy/7s-rummy-rest-api/news/get-latest-news');
 
    server_request.done(function (data) {
        
        let obj = data.data.news;      
        var newsHtml='';

        for (let i = 0; i < obj.length; i++) {
            newsHtml = newsHtml + '<div class="row"><div class="col-lg-5 mt-4"><div class="member d-flex align-items-start"><div class="pic"><img src="'+obj[i].image+'" class="img-fluid" alt=""></div></div></div><div class="col-lg-7 mt-4 mt-lg-0"><div class="member d-flex align-items-start"><div class="member-info1"><h4>'+obj[i].title+'</h4><p>'+obj[i].sub_description+'<a class="btn3" href="demo?news-id='+obj[i].id+'" style="text-decoration: underline;">More</a></p></div><div class="row"><div class="col-md-12"><h6 class="head" style="color: #B0C5FF;margin-top: 10px;margin-left: 55px;width: 82px;">'+obj[i].created_at+'</h6></div></div></div></div></div>';
        }

        $("#news").append(newsHtml);

    });

})(jQuery);





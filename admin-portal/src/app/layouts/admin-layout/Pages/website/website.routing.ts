import { Routes } from '@angular/router';

import { WebSettingComponent } from './web-setting/web-setting.component';
import { SocialMediaComponent } from './social-media/social-media.component';
import { PostFaqComponent } from './post-faq/post-faq.component';
import { PostNewsComponent } from './post-news/post-news.component';

import { AddNewsComponent } from './post-news/add-news/add-news.component';
import { EditNewsComponent } from './post-news/edit-news/edit-news.component';

import { AuthGuardService } from '../../../../services/authguard.service';

export const WebsiteRoutes: Routes = [
    
    { path: 'web-setting',  component: WebSettingComponent, canActivate : [AuthGuardService], data:{id:'get_web_setting'}},
    { path: 'social-media',  component: SocialMediaComponent, canActivate : [AuthGuardService], data:{id:'get_social_media'}},
    { path: 'faq-list',  component: PostFaqComponent, canActivate : [AuthGuardService], data:{id:'get_faq'}},

    { path: 'news-list',  component: PostNewsComponent, canActivate : [AuthGuardService], data:{id:'get_news_list'}},
    { path: 'add-news',  component: AddNewsComponent, canActivate : [AuthGuardService], data:{id:'add_news_list'}},
    { path: 'edit-news/:news_id',  component: EditNewsComponent, canActivate : [AuthGuardService], data:{id:'edit_news_list'}}

];
import { Routes } from '@angular/router';

import { EmailTemplateComponent } from './email-template/email-template.component';
import { EditEmailTemplateComponent } from './email-template/edit-email-template/edit-email-template.component';

import { AuthGuardService } from '../../../../services/authguard.service';

export const EmailToPlayersRoutes: Routes = [

    { path: 'mail-template',   component: EmailTemplateComponent, canActivate : [AuthGuardService], data:{id:'get_mail_templates'} },
    { path: 'edit-mail-template/:mail_tem_id',   component: EditEmailTemplateComponent, canActivate : [AuthGuardService], data:{id:'edit_mail_templates'} },


];
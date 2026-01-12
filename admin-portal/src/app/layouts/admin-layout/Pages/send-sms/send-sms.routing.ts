import { Routes } from '@angular/router';

// import { SendNewSmsComponent } from './send-new-sms/send-new-sms.component';
// import { SendSmsDetailsComponent } from './send-sms-details/send-sms-details.component';
import { SmsTemplatesComponent } from './sms-templates/sms-templates.component';

import { AuthGuardService } from '../../../../services/authguard.service';

export const SendSMSRoutes: Routes = [

    // { path: 'send-new-sms', component: SendNewSmsComponent, canActivate : [AuthGuardService], data:{id:'send_new_sms'} },
    // { path: 'send-sms-details', component: SendSmsDetailsComponent, canActivate : [AuthGuardService], data:{id:'send_sms_details'} },
    { path: 'sms-template', component: SmsTemplatesComponent, canActivate : [AuthGuardService], data:{id:'get_sms_templates'} },

];
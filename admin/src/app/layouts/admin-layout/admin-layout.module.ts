
import { NgModule } from '@angular/core';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';

import { ClipboardModule } from 'ngx-clipboard';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { ModalModule } from 'ngx-bootstrap';
import {NgxPaginationModule} from 'ngx-pagination';
import { NgxDatatableModule } from '@swimlane/ngx-datatable';

//Route
import { AdminLayoutRoutes } from './admin-layout.routing';

//Service
import { JwtInterceptor } from '../../helpers/jwt.interceptor';
import { AuthGuardService } from '../../services/authguard.service';

//Module
import { ProfileModule } from '../../layouts/admin-layout/pages/profile/profile.module';

import {DashboardModule } from '../../layouts/admin-layout/pages/dashboard/dashboard.module';

import { AdminModule } from '../../layouts/admin-layout/pages/admin/admin.module';
import { AdminRoleModule } from '../../layouts/admin-layout/pages/admin-role/admin-role.module';

import { UsersModule } from '../../layouts/admin-layout/pages/users/users.module';
import { UserKycVerifyModule } from '../../layouts/admin-layout/pages/users-kyc-verify/users-kyc-verify.module';
import { UsersWithdrawalModule } from './Pages/users-withdrawal-request/users-withdrawal-request.module';

import { GameTableModule } from './Pages/game-table/game-table.module';
import { GameSettingsModule } from './Pages/game-settings/game-settings.module';
import { TournamentModule } from '../../layouts/admin-layout/pages/tournaments/tournaments.module';

import { ConfigurationModule } from '../../layouts/admin-layout/pages/configuration/configuration.module';

import { WebsiteModule } from './Pages/website/website.module';

import { EmailToPlayersModule } from '../../layouts/admin-layout/pages/email-to-players/email-to-players.module';
import { SendSMSModule } from '../../layouts/admin-layout/pages/send-sms/send-sms.module';


@NgModule({
  imports: [
    CommonModule,
    RouterModule.forChild(AdminLayoutRoutes),
    FormsModule,
    ReactiveFormsModule,
    HttpClientModule,
    NgbModule,
    ClipboardModule,
    ModalModule.forRoot(),
    NgxDatatableModule,
    NgxPaginationModule,

    ProfileModule,
    DashboardModule,
    AdminModule, AdminRoleModule,
    UsersModule, UserKycVerifyModule, UsersWithdrawalModule,
    GameTableModule, GameSettingsModule,TournamentModule,    
    ConfigurationModule,
    WebsiteModule,
    EmailToPlayersModule, SendSMSModule, 
    

  ],
  declarations: [
    

  ],providers:[AuthGuardService, { provide: HTTP_INTERCEPTORS, useClass: JwtInterceptor, multi: true }]
})
//providers: [{ provide: HTTP_INTERCEPTORS, useClass: JwtInterceptor, multi: true } ], { provide: HTTP_INTERCEPTORS, useClass: JwtInterceptor, multi: true }
export class AdminLayoutModule {}
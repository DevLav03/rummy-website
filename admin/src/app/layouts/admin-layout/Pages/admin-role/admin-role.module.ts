
import { NgModule } from '@angular/core';
import { HttpClientModule } from '@angular/common/http';
import { RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';

import { ClipboardModule } from 'ngx-clipboard';

import { AdminRoleRoutes } from './admin-role.routing';

import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { ModalModule } from 'ngx-bootstrap';
import { NgxDatatableModule } from '@swimlane/ngx-datatable';
import {NgxPaginationModule} from 'ngx-pagination';

import { AuthGuardService } from '../../../../services/authguard.service';

import { RolesComponent } from './roles/roles.component';
import { ScopePermissionComponent } from './scope-permission/scope-permission.component';

@NgModule({
  imports: [
    CommonModule,
    RouterModule.forChild(AdminRoleRoutes),
    FormsModule,
    ReactiveFormsModule,
    HttpClientModule,
    NgbModule,
    ClipboardModule,
    ModalModule.forRoot(),
    NgxDatatableModule,
    NgxPaginationModule
    
  ],
  declarations: [

    RolesComponent,
    ScopePermissionComponent    

  ],providers:[AuthGuardService]
})
export class AdminRoleModule {}
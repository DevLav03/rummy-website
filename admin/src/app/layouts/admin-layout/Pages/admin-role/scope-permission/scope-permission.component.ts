import { Component, OnInit,ViewChild  } from '@angular/core';
import { HttpClient } from "@angular/common/http";
import { ActivatedRoute } from '@angular/router';

import { DatatableComponent } from '@swimlane/ngx-datatable';

import { NotificationService } from '../../../../../services/notification.service';
import { AuthService } from 'src/app/services/auth.service';

@Component({
  selector: 'app-scope-permission',
  templateUrl: './scope-permission.component.html',
  styleUrls: ['./scope-permission.component.scss']
})

export class ScopePermissionComponent implements OnInit {

  public getData : any= []; //Data
  public roleType : any;

  public role_id: number;

  public msg: any; //Alert Msg

  //Table
  groups = [];
  editing = {};
  rows = [];
  rows_temp = [];
  final_scope = [];
  current_role : any;

  @ViewChild(DatatableComponent) public scopeTable: DatatableComponent; //ngx table
  public currentPageLimit: number = 10;
  public currentVisible: number = 3; 
  public readonly pageLimitOptions = [
    {value: 5},
    {value: 10},
    {value: 25},
    {value: 50},
    {value: 100},
  ];
  public readonly visibleOptions = [
    {value: 1},
    {value: 3},
    {value: 5},
    {value: 10},
  ];

  constructor(private httpClient: HttpClient,private route: ActivatedRoute, private authService: AuthService, private notifyService : NotificationService) { 
  
    this.role_id = this.route.snapshot.params['role_id'];

  }

  ngOnInit() { 

    this.getAllScope();

  }

  ngAfterViewInit(){ 
    
  }

  public checkScope(scopeName: string){

    let result = this.authService.checkScopeExists(scopeName);
    return result;

  }

  public getAllScope(){
    this.httpClient.get('admin-scope/get-all-scope').subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.rows_temp = data.data.scope_list;  
        this.onSubmit(); 
        console.log(this.rows_temp);
      }else{
        this.error();
      } 
    });
    
  }

  public getRoleScope(){

    this.httpClient.get('role/get-role/' + this.role_id).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.getData =  data.data.roles[0].scope_list;
        console.log('getRoleData1', this.getData);
      }else{
        this.error();
      }
    });

  }

  public onSubmit(){ 
   
    
    this.rows=[];
    let jsonInput :any = 
    {
      "role_type" : this.role_id
    }; 

    this.current_role = jsonInput.role_type;

    this.httpClient.get('role/get-role/'+jsonInput.role_type).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        let obj = JSON.parse(data.data.roles[0].scope_list);
        this.getData =  obj; 
        this.bindAvailableScopes();
        //console.log('getRoleData2', this.getData, this.rows, this.rows_temp); 
        //console.log('rows_temp', this.rows_temp); 
        this.rows=[];
        this.rows=[...this.rows_temp];
      }else{
        this.error();
      } 
    });

   
  }

  bindAvailableScopes(){
   
    for(let item = 0; item < this.rows_temp.length; item++){
      let isChecked=this.compareScopes(this.rows_temp[item].scope, this.getData);
      this.rows_temp[item].isChecked=isChecked;
      //this.rows.push( this.rows_temp[item]);
      //console.log( this.rows_temp[item],this.rows);
    }
    
  }
  checkCheckBoxvalue(event :any,scope:string){
    this.final_scope=[];
    //console.log(event,event.target.checked,scope);
    for(let item = 0; item < this.rows_temp.length; item++){
      //console.log("Compare",this.rows_temp[item] , this.rows_temp[item].isChecked,scope,this.rows_temp[item].isChecked);

      if(this.rows_temp[item].scope == scope){
        this.rows_temp[item].isChecked=event.target.checked;
      }
      
      if(this.rows_temp[item].isChecked == true){
        //console.log("Compare",this.rows_temp[item].scope );
        this.final_scope.push(this.rows[item].scope);
      }
     
      
    }
    this.rows=[...this.rows_temp];
    this.scopeTable.recalculate();
    let jsonInput :any = 
    {
      "scope_list" : JSON.stringify(this.final_scope)
    }; 
    
    this.httpClient.post('admin-scope/update-role-scope/'+ this.current_role,jsonInput).subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.success('Update Successfully!');
        
      }else if(data.response == 'failure'){
        this.msg = data.err_message;
        this.failed(this.msg);
        
      }else{
        this.error();
      } 
    },error => {
      this.error();
    });
   
    //console.log("this.final_scope",this.rows,this.final_scope.toString());
  }
  compareScopes(scope, roleScopeList){
    for(let item = 0; item < roleScopeList.length; item++){
      //console.log("Compare",roleScopeList[item] , scope);

      if(roleScopeList[item] == scope){
        return true;
      }
    }
    return false;
  }
  
  
  //Table
  getGroupRowHeight(group, rowHeight) {
    let style = {};

    style = {
      height: group.length * 40 + 'px',
      width: '100%'
    };

    return style;
  }
  toggleExpandGroup(group) {
    //console.log('Toggled Expand Group!', group);
    this.scopeTable.groupHeader.toggleExpandGroup(group);
  }
  //Table End


  // Notification
  public success(msg: string){
    this.notifyService.showSuccess(msg, "Success")
  }
  public failed(msg: string){
    this.notifyService.showError(msg, "Error") 
  }
  public error(){
    this.notifyService.showWarning("Something Went Wrong!", "Warning") 
  }
  onDetailToggle(event) {
    //console.log('Detail Toggled', event);
  }
}



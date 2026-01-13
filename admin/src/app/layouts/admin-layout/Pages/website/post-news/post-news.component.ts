import { Component, OnInit,ViewChild } from '@angular/core';
import { HttpClient } from "@angular/common/http";
import { Router,ActivatedRoute } from '@angular/router';

import { BsModalService, BsModalRef } from 'ngx-bootstrap/modal';
import { DatatableComponent } from '@swimlane/ngx-datatable';

import { NotificationService } from '../../../../../services/notification.service';
import { AuthService } from 'src/app/services/auth.service';

import { BlockFormComponent } from 'src/app/forms/block-form/block-form.component';
import { DeleteFormComponent } from '../../../../../forms/delete-form/delete-form.component';
import { DomSanitizer } from '@angular/platform-browser';

@Component({
  selector: 'app-post-news',
  templateUrl: './post-news.component.html',
  styleUrls: ['./post-news.component.scss']
})

export class PostNewsComponent implements OnInit {

  public getData : any= []; //Data
  
  public name: any;
  public role: any;

  public removeId: any;

  public msg: any; //Alert Msg
  public temp : any= []; //Search

  bsModalRef: BsModalRef; //Modal

  @ViewChild(DatatableComponent) public table: DatatableComponent; //ngx table
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

  constructor(private sanitizer:DomSanitizer, private httpClient: HttpClient,private modalService: BsModalService,  private authService: AuthService, private _router: Router, private notifyService : NotificationService, private route: ActivatedRoute) { 

    this.name = sessionStorage.getItem("name");
    this.role = sessionStorage.getItem("role");
    
  }

  ngOnInit() {

    this.getAllData();

  }

  public checkScope(scopeName: string){

    let result = this.authService.checkScopeExists(scopeName);

    return result;

  }


  //Get Data
  public getAllData() {
   
    this.httpClient.get('news/get-all-news').subscribe((data:any= {}) => {
      if(data.response == 'success'){
        this.getData =  data.data.news;
        this.temp = [...data.data.news];
      }else if(data.response == 'failure'){
        this.msg = data.err_message;
        this.failed(this.msg);
      }else{
        this.error();
      } 
    });
  }

  public addData(){
    this._router.navigate(['add-news'])
  }

  public editData(id: number){
    this._router.navigate(['edit-news', id])
  }

  public changeActive(item: any){
    if(item.status == 0){
      const initialState = {
        config: {
          title: "News",
          type: "Active",
          value: 1
        },
        dataInfo: {
          url: 'news/status/' + item.id + '/' + 1
        }
      };
      this.bsModalRef = this.modalService.show(BlockFormComponent, {
        initialState,
        animated: true,
        backdrop: 'static',
        class: 'modal-md'
      });
      this.bsModalRef.content.event.subscribe(res => {
        this.temp.map(obj => {
          if (obj.id == item.id) {
            obj.status= res.status;
          }
        });
        this.getData=[...this.temp];
        this.table.recalculate();
        this.success('Active Successfully');
      });
    }else if (item.status == 1){
      const initialState = {
        config: {
          title: "News",
          type: "InActive",
          value: 0
        },
        dataInfo: {
          url: 'news/status/' + item.id + '/' + 0
        }
      };
      this.bsModalRef = this.modalService.show(BlockFormComponent, {
        initialState,
        animated: true,
        backdrop: 'static',
        class: 'modal-md'
      });
      this.bsModalRef.content.event.subscribe(res => {
        this.temp.map(obj => {
          if (obj.id == item.id) {
            obj.status= res.status;
          }
        });
        this.getData=[...this.temp];
        this.table.recalculate();
        this.success('InActive Successfully');
      });
    }
  }

  public deleteData(id: number){
    const initialState = {
      config: {
        title: "News",
        id: id
      },
      dataInfo: {
        url: 'news/delete-news/' + id
      }
    };
    this.bsModalRef = this.modalService.show(DeleteFormComponent, {
      initialState,
      animated: true,
      backdrop: 'static',
      class: 'modal-md'
    });
    this.bsModalRef.content.event.subscribe(res => {

      this.removeId = res.delete_id;

      const removeItinerary = (removeId: number) => {

        const data = this.temp.filter(obj => obj.id !== removeId); 

        return data;
      }

      let data = removeItinerary(this.removeId);

      this.temp=[...data];
      this.getData=[...data];
      this.table.recalculate();
    });
  }

  //ngx Table
  public onLimitChange(limit: any): void {
    this.changePageLimit(limit);
    this.table.limit = this.currentPageLimit;
    this.table.recalculate();
    setTimeout(() => {
      if (this.table.bodyComponent.temp.length <= 0) {
        this.table.offset = Math.floor((this.table.rowCount - 1) / this.table.limit);
      }
    });
  }
  public onVisibleChange(visible: any): void {
    this.currentVisible = parseInt(visible, 10);
  }
  private changePageLimit(limit: any): void {
    this.currentPageLimit = parseInt(limit, 10);
  }
  updateFilter(event) {
    const val = event.target.value.toLowerCase();
    if(val !== null && val.toString().trim() !=''){  
      const temp = this.temp.filter(function (d) {
        if(d.title !== null){
        return ((d.title !==null && d.title.toLowerCase().indexOf(val) !== -1)) || !val ;
      }      
    });  
    this.getData = temp;  // update the rows
    this.table.offset = 0;
    }else{
      this.getData = [...this.temp];
      this.table.offset = 0;
    }
  }
  //End

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
    
}





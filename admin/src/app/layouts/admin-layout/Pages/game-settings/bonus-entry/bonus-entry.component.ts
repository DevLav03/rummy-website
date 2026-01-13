import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-bonus-entry',
  templateUrl: './bonus-entry.component.html',
  styleUrls: ['./bonus-entry.component.scss']
})
export class BonusEntryComponent implements OnInit {

  public name: any;
  public role: any;

  constructor() { 

    this.name = sessionStorage.getItem("name");
    this.role = sessionStorage.getItem("role");
    
  }

  ngOnInit(): void {
  }

}



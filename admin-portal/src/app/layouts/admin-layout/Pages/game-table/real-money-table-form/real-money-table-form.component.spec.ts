import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RealMoneyTableFormComponent } from './real-money-table-form.component';

describe('RealMoneyTableFormComponent', () => {
  let component: RealMoneyTableFormComponent;
  let fixture: ComponentFixture<RealMoneyTableFormComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ RealMoneyTableFormComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(RealMoneyTableFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

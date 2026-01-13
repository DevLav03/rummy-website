import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RealMoneyTableComponent } from './real-money-table.component';

describe('RealMoneyTableComponent', () => {
  let component: RealMoneyTableComponent;
  let fixture: ComponentFixture<RealMoneyTableComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ RealMoneyTableComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(RealMoneyTableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

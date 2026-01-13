import { ComponentFixture, TestBed } from '@angular/core/testing';

import { TournamentsTransactionsComponent } from './tournaments-transactions.component';

describe('TournamentsTransactionsComponent', () => {
  let component: TournamentsTransactionsComponent;
  let fixture: ComponentFixture<TournamentsTransactionsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ TournamentsTransactionsComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(TournamentsTransactionsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

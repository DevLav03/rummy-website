import { ComponentFixture, TestBed } from '@angular/core/testing';

import { BonusEntryComponent } from './bonus-entry.component';

describe('BonusEntryComponent', () => {
  let component: BonusEntryComponent;
  let fixture: ComponentFixture<BonusEntryComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ BonusEntryComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(BonusEntryComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

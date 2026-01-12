import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SendNewSmsComponent } from './send-new-sms.component';

describe('SendNewSmsComponent', () => {
  let component: SendNewSmsComponent;
  let fixture: ComponentFixture<SendNewSmsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SendNewSmsComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(SendNewSmsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

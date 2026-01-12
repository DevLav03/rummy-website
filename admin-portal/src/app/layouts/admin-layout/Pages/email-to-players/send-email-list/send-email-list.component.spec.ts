import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SendEmailListComponent } from './send-email-list.component';

describe('SendEmailListComponent', () => {
  let component: SendEmailListComponent;
  let fixture: ComponentFixture<SendEmailListComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SendEmailListComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(SendEmailListComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

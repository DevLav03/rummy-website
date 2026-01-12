import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UsersLogComponent } from './users-log.component';

describe('UsersLogComponent', () => {
  let component: UsersLogComponent;
  let fixture: ComponentFixture<UsersLogComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ UsersLogComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(UsersLogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

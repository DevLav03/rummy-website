import { ComponentFixture, TestBed } from '@angular/core/testing';

import { TournamentsFormComponent } from './tournaments-form.component';

describe('TournamentsFormComponent', () => {
  let component: TournamentsFormComponent;
  let fixture: ComponentFixture<TournamentsFormComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ TournamentsFormComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(TournamentsFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

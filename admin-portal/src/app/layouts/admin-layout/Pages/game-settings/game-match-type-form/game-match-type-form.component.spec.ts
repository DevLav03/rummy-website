import { ComponentFixture, TestBed } from '@angular/core/testing';

import { GameMatchTypeFormComponent } from './game-match-type-form.component';

describe('GameMatchTypeFormComponent', () => {
  let component: GameMatchTypeFormComponent;
  let fixture: ComponentFixture<GameMatchTypeFormComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ GameMatchTypeFormComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(GameMatchTypeFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

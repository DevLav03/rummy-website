import { ComponentFixture, TestBed } from '@angular/core/testing';

import { GameMatchTypeComponent } from './game-match-type.component';

describe('GameMatchTypeComponent', () => {
  let component: GameMatchTypeComponent;
  let fixture: ComponentFixture<GameMatchTypeComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ GameMatchTypeComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(GameMatchTypeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

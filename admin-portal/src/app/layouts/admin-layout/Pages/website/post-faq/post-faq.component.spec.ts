import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PostFaqComponent } from './post-faq.component';

describe('PostFaqComponent', () => {
  let component: PostFaqComponent;
  let fixture: ComponentFixture<PostFaqComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ PostFaqComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(PostFaqComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

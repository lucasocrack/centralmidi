import { ComponentFixture, TestBed } from '@angular/core/testing';

import { BasicInputComponent } from './basic-input.component';

describe('BasicInputComponent', () => {
  let component: BasicInputComponent;
  let fixture: ComponentFixture<BasicInputComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [BasicInputComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(BasicInputComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

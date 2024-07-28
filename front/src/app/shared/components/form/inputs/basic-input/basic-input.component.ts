import { Component, Input } from '@angular/core';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { AbstractControl, FormControl, ReactiveFormsModule } from '@angular/forms';
import { NgxMaskDirective, provideNgxMask } from 'ngx-mask';
import { MatTooltipModule } from '@angular/material/tooltip';
import { formErrorPipe } from '../../../../pipes/formError.pipe';
import { MyErrorStateMatcher } from '../../form.service';
import { Question } from '../../../../types/commom.types';

@Component({
  selector: 'app-basic-input',
  standalone: true,
  imports: [
    MatFormFieldModule,
    MatInputModule,
    ReactiveFormsModule,
    NgxMaskDirective,
    MatTooltipModule,
    formErrorPipe
  ],
  providers: [
    provideNgxMask(),
  ],
  templateUrl: './basic-input.component.html',
  styleUrls: ['./basic-input.component.scss', '../input-common.scss']
})
export class BasicInputComponent {
  @Input({required: true}) question!: Question
  formControl!: FormControl
  @Input({required: true}) set control(value: FormControl | AbstractControl) {
    this.formControl = value as FormControl
  }
  matcher = new MyErrorStateMatcher()
}

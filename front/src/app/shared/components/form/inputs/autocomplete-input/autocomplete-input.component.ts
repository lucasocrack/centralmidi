import { Observable } from 'rxjs';
import { Component, Input } from '@angular/core';
import {
  AbstractControl,
  FormControl,
  ReactiveFormsModule,
} from '@angular/forms';
import { GenericOptions, Question } from 'src/app/shared/types/commom.types';
import { MyErrorStateMatcher } from 'src/app/shared/components/form/form.service';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatTooltipModule } from '@angular/material/tooltip';
import { formErrorPipe } from 'src/app/shared/pipes/formError.pipe';
import { AsyncPipe } from '@angular/common';
import { MatAutocompleteModule } from '@angular/material/autocomplete';
import { MatInputModule } from '@angular/material/input';

@Component({
  selector: 'app-autocomplete-input',
  standalone: true,
  imports: [
    MatFormFieldModule,
    ReactiveFormsModule,
    MatTooltipModule,
    formErrorPipe,
    AsyncPipe,
    MatAutocompleteModule,
    MatInputModule,
  ],
  templateUrl: './autocomplete-input.component.html',
  styleUrls: ['./autocomplete-input.component.scss', '../input-common.scss'],
})
export class AutocompleteInputComponent {
  @Input({ required: true }) question!: Question;
  formControl!: FormControl;
  @Input({ required: true }) set control(value: FormControl | AbstractControl) {
    this.formControl = value as FormControl;
  }
  @Input({ required: true }) options!: Observable<GenericOptions[]>;
  matcher = new MyErrorStateMatcher();

  displayFn(option: GenericOptions): string {
    return option?.name ? option.name : '';
  }
}

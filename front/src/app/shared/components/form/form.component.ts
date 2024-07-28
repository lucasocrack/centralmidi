import { FormService } from './form.service';
import { Component, DestroyRef, inject, Input, OnInit } from '@angular/core';
import { GenericOptions, Question, QuestionType, SearchAutocomplete, SearchService } from '../../types/commom.types';
import { FormControl, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { BasicInputComponent } from './inputs/basic-input/basic-input.component';
import { PasswordInputComponent } from './inputs/password-input/password-input.component';
import { AutocompleteInputComponent } from './inputs/autocomplete-input/autocomplete-input.component';
import { Observable } from 'rxjs';

@Component({
  selector: 'app-form',
  standalone: true,
  imports: [
    ReactiveFormsModule,
    BasicInputComponent,
    PasswordInputComponent,
    AutocompleteInputComponent
  ],
  templateUrl: './form.component.html',
  styleUrl: './form.component.scss'
})
export class FormComponent implements OnInit {
  private formService = inject(FormService)
  private destroyRef = inject(DestroyRef)

  @Input({required: true}) questions!: Question[]
  @Input({required: true}) form!: FormGroup<{[key: string]: FormControl}>
  types = QuestionType
  autocompleteServices: { 
    [key: string]: Observable<GenericOptions[]> 
  } = {};

  ngOnInit(): void {
    this.questions.forEach(question => {
      if (question.type === QuestionType.AUTOCOMPLETE) {
        const params: SearchAutocomplete = question.isServiceSearch ? {
          isServiceSearch: true,
          searchService: question.searchService as SearchService,
          destroyRef: this.destroyRef
        } : {
          isServiceSearch: false,
          options: question.options || [],
          destroyRef: this.destroyRef
        }
        const formControl = this.form.get(question.key)

        if (!formControl) return

        this.autocompleteServices[question.key] = formControl.valueChanges.pipe(
          this.formService.searchAutocomplete(params)
        )
        
      }

    })
  }
}

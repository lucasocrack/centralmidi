import { Component, inject } from '@angular/core';
import { FormService } from '../../../shared/components/form/form.service';
import { GenericOptions, Question, ValidatorsType } from '../../../shared/types/commom.types';
import { FormComponent } from '../../../shared/components/form/form.component';
import { MatCardModule } from '@angular/material/card';
import { of } from 'rxjs';

const options: GenericOptions[] = [
  { name: 'Alabama', value: 'Alabama' },
  { name: 'Alaska', value: 'Alaska' },
  { name: 'Arizona', value: 'Arizona' },
  { name: 'California', value: 'California' },
  { name: 'Colorado', value: 'Colorado' },
  { name: 'Connecticut', value: 'Connecticut' },
  { name: 'Idaho', value: 'Idaho' },
  { name: 'Illinois', value: 'Illinois' },
  { name: 'Kansas', value: 'Kansas' },
]

@Component({
  selector: 'app-forms',
  standalone: true,
  imports: [FormComponent, MatCardModule],
  templateUrl: './forms.component.html',
  styleUrl: './forms.component.scss'
})
export class FormsComponent {
  private formService = inject(FormService)
  questions: Question[] = [
    { 
      key: 'q1', 
      type: 'text', 
      label: 'Text Question', 
      class: 'col-4', 
      validators: [{type: ValidatorsType.REQUIRED}], 
      startIcon: {icon: 'home'} 
    },
    { 
      key: 'q2', 
      type: 'email', 
      label: 'Email Question', 
      class: 'col-4', 
      validators: [{type: ValidatorsType.EMAIL}] 
    },
    { 
      key: 'q3', 
      type: 'password', 
      label: 'Password Question', 
      class: 'col-4' 
    },
    { 
      key: 'q4', 
      type: 'autocomplete', 
      label: 'Autocomplete Question', 
      class: 'col-4',
      isServiceSearch: false,
      options: options
    },
    { 
      key: 'q5', 
      type: 'autocomplete', 
      label: 'Autocomplete Service Question', 
      class: 'col-4',
      isServiceSearch: true,
      searchService: (query: string) => {    
        return of(options.filter(option => option.value.toLowerCase().includes(query)))
      }
    },
  ]
  form = this.formService.createFormGroup(this.questions)
}

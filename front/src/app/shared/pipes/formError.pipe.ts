import { Pipe, PipeTransform } from '@angular/core';
import { ValidationErrors } from '@angular/forms';

interface ErrorMap {
  [key: string]: string;
}

interface ErrorProps {
  valueKey: string[]
  errorsKey: string[]
}

@Pipe({
  name: 'formError',
  standalone: true
})
export class formErrorPipe implements PipeTransform {
  errors: ErrorMap = {
    required: 'Este campo é obrigatório',
    email: 'E-mail inválido',
    matDatepickerParse: 'Data inválida',
    invalidTime: 'Hora inválida'
  }

  generateMultiErrors({ valueKey, errorsKey }: ErrorProps): string | null {
    const errorKey = errorsKey.find(key => key === valueKey[0]);

    if (errorKey) {
      return this.errors[errorKey];
    }
    return null
  }

  transform(value: ValidationErrors | null, ...args: unknown[]): unknown {
    if (!value) {
      return null;
    }
    const valueKey = Object.keys(value);
    const errorsKey = Object.keys(this.errors);
    console.log(valueKey);
    if (valueKey.length > 1) {
      let concatErrors = ''

      for (const [index, item] of valueKey.entries()) {

        concatErrors += this.generateMultiErrors({ valueKey: [item], errorsKey })

        if (valueKey.length !== index + 1) {
          concatErrors += ' | '
        }
      }

      return concatErrors
    }

    return this.generateMultiErrors({ valueKey, errorsKey });
  }
}

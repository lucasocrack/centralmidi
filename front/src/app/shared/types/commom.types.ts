import { DestroyRef } from "@angular/core"
import { Observable } from "rxjs"

export type SidebarItems = {
  title: string
  children?: SidebarChildren[]
}

export type SidebarChildren = {
  title: string
  icon: string
  badge?: string
  route?: string
  subChildren?: SidebarSubChildren[]
}

export type SidebarSubChildren = {
  title: string
  route: string
  badge?: string
}

export enum QuestionType {
  TEXT = 'text',
  EMAIL = 'email',
  AUTOCOMPLETE = 'autocomplete',
  /* MULTI_AUTOCOMPLETE = 'multi-autocomplete', */
  DATEPICKER = 'datepicker',
  FILE_UPLOAD = 'file-upload',
  MULTI_FILE_UPLOAD = 'multi-file-upload',
  PASSWORD = 'password',
  SELECT = 'select',
  MULTI_SELECT = 'multi-select',
  TEXTAREA = 'textarea',
  TIMEPICKER = 'timepicker',
}

export type QuestionTypeValues = `${QuestionType}`

export enum ValidatorsType {
  REQUIRED = 'required',
  EMAIL = 'email',
  MIN = 'min',
  MAX = 'max'
}

export type ValidatorsTypeValues = `${ValidatorsType}`

export type Question = {
  key: string
  type: QuestionTypeValues
  class: string
  startIcon?: QuestionIcon
  endIcon?: QuestionIcon
  label?: string
  placeholder?: string
  hint?: string
  inputClass?: string
  prefix?: string
  suffix?: string
  minlength?: number
  maxlength?: number
  pattern?: RegExp
  inputTooltip?: string
  mask?: string
  validators?: QuestionValidators[]
  isDisabled?: boolean
  value?: string | number
  searchService?: SearchService
  isServiceSearch?: boolean
  options?: GenericOptions[] 
}

export type QuestionValidators = {
  type: ValidatorsTypeValues
  value?: string | number
}

export type QuestionIcon = {
  icon: string
  tootip?: string
  color?: string
}

export type SearchAutocomplete = {
  isServiceSearch: true
  searchService: SearchService
  destroyRef: DestroyRef
  minLength?: number
  debounce?: number
} | {
  isServiceSearch: false
  destroyRef: DestroyRef
  minLength?: number
  debounce?: number
  options: GenericOptions[] 
}

export type GenericOptions = {
  value: string;
  name: string;
  [key: string]: any;
};

export type SearchService = (query: string) => Observable<GenericOptions[]>
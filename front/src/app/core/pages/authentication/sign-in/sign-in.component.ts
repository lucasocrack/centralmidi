import {Component, inject, OnInit} from '@angular/core';
import { MatButtonModule } from '@angular/material/button';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { RouterLink, Router } from '@angular/router';
import {FormBuilder, FormControl, FormGroup, ReactiveFormsModule, Validators} from '@angular/forms';
import { NgIf } from '@angular/common';
import { CustomizerSettingsService } from '../../../../shared/components/customizer-settings/customizer-settings.service';
import {AuthLoginDto, AuthService} from "../../../../services/auth.service";
import {environment} from "../../../../../environment/environment";

@Component({
  selector: 'app-sign-in',
  standalone: true,
  imports: [RouterLink, MatFormFieldModule, MatInputModule, MatButtonModule, MatCheckboxModule, ReactiveFormsModule, NgIf],
  templateUrl: './sign-in.component.html',
  styleUrl: './sign-in.component.scss'
})
export class SignInComponent implements OnInit {

  private authService = inject(AuthService);
  isToggled = false;
  errorMessage: string | null = null;

  constructor(
    private router: Router,
    public themeService: CustomizerSettingsService
  ) {}

  ngOnInit() {
    this.themeService.isToggled$.subscribe(isToggled => {
      this.isToggled = isToggled;
    });
  }
  protected form = new FormGroup({
    identifier: new FormControl<string>('', [Validators.required]),
    password: new FormControl<string>('', [Validators.required]),
  });

  hide = true;

  onSave() {
    if (this.form.invalid) return;

    const payload = this.form.value as AuthLoginDto;
    this.authService.login(payload).subscribe({
      next:(data) => {
        localStorage.setItem(`${environment.localStorageKey}token`, data.access_token);
        console.log(data);
        this.errorMessage = null;
        this.router.navigate(['/']);
      },
      error:(error) => {
        if (error.status === 401) {
          this.errorMessage = 'Usuário ou senha inválidos.';
        } else {
          this.errorMessage = 'Ocorreu um erro. Tente novamente mais tarde.';
        }
        console.error(error);
      }
      }
    );
  }

}

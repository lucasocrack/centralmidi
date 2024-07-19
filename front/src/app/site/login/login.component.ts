import {Component, inject, OnInit} from '@angular/core';
import {FormBuilder, FormControl, FormGroup, ReactiveFormsModule, Validators} from '@angular/forms';
import {AuthLoginDto, AuthService} from "../../services/auth.service";
import {environment} from "../../../environment/environment";

@Component({
  selector: 'app-login',
  standalone: true,
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss'],
  imports: [ ReactiveFormsModule ]
})
export class LoginComponent {
  private authService = inject(AuthService);
  protected form = new FormGroup({
    email: new FormControl<string>('', [Validators.required, Validators.email]),
    password: new FormControl<string>('', [Validators.required]),
  });

  onSave() {
    if (this.form.invalid) return;

    const payload = this.form.value as AuthLoginDto;
    this.authService.login(payload).subscribe({
      next:(data) => {
        localStorage.setItem(`${environment.localStorageKey}token`, data.accessToken);
        console.log(data);
      },
      error:(error) => {
        console.error(error);
      }
    }
    );
  }
}

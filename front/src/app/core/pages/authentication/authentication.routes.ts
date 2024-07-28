import { Routes } from '@angular/router';

export const authenticationRoutes: Routes = [
  {
    path: '',
    loadComponent: () => import('./sign-in/sign-in.component').then( m => m.SignInComponent),
    data: { title: 'Theme - Sign-in' }
  },
  {
    path: 'sign-up',
    loadComponent: () => import('./sign-up/sign-up.component').then( m => m.SignUpComponent),
    data: { title: 'Theme - Sign-up' }
  },
  {
    path: 'forgot-password',
    loadComponent: () => import('./forgot-password/forgot-password.component').then( m => m.ForgotPasswordComponent),
    data: { title: 'Theme - Forgot password' }
  },
  {
    path: 'reset-password', 
    loadComponent: () => import('./reset-password/reset-password.component').then( m => m.ResetPasswordComponent),
    data: { title: 'Theme - Reset password' }
  },
  {
    path: 'confirm-email', 
    loadComponent: () => import('./confirm-email/confirm-email.component').then( m => m.ConfirmEmailComponent),
    data: { title: 'Theme - Confirm email' }
  },
  {
    path: 'logout', 
    loadComponent: () => import('./logout/logout.component').then( m => m.LogoutComponent),
    data: { title: 'Theme - Logout' }
  },
];

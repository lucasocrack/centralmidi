import { Routes } from '@angular/router';
import { authGuard } from '../core/guards/auth.guard';

export const privateRoutes: Routes = [
  {
    path: '',
    canActivate: [authGuard],
    loadComponent: () => import('./pages/dashboard/dashboard.component').then( m => m.DashboardComponent),
    data: { title: 'Theme - Dashboard' }
  },
  {
    path: 'forms',
    canActivate: [authGuard],
    loadComponent: () => import('./pages/forms/forms.component').then( m => m.FormsComponent),
    data: { title: 'Theme - Forms' }
  },
];

import {inject, Injectable} from '@angular/core';
import {environment} from "../../environment/environment";
import {HttpClient} from "@angular/common/http";

export interface AuthLoginDto {
  email: string;
  password: string;
}

export interface AuthLoginResponse {
  accessToken: string;
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private baseUrl = environment.api;
  private http = inject(HttpClient)

  constructor() {}
  login(payload: AuthLoginDto) {
  return this.http.post<AuthLoginResponse>(`${this.baseUrl}/auth/login`, payload);
  }
}

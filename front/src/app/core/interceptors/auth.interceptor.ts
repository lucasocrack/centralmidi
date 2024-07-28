import { HttpHeaders, HttpInterceptorFn } from '@angular/common/http';

export const InterceptorSkip = 'X-Skip-Interceptor'
export const InterceptorSkipHeader = new HttpHeaders({
  'X-Skip-Interceptor': '',
})

export const authInterceptor: HttpInterceptorFn = (req, next) => {
  return next(req);
};

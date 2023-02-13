// @ts-ignore: unexported from inertia
import { router } from '@inertiajs/vue3';
import { ILoan } from "../loanEntity";
import { ILoanInstallment } from '../loanInstallmentEntity';

interface IFormDataWithId {
  id?: number;
}
const getSaveMethod = (formData: IFormDataWithId) => {
  let url = '/loans';
  let method = 'post';

  if (formData.id) {
    url =  `/loans/${formData.id}`;
    method = 'put' 
  } 
  
  return router[method].bind(router, url);
}
export const saveLoan = (loanData: ILoan, installments: ILoanInstallment[]) => {
    const saveMethod = getSaveMethod(loanData);
    console.log(saveMethod);
    return new Promise((resolve, reject) => {
        return saveMethod({
            ...loanData,
            installments
        }, {
            onSuccess(data: any) {
                resolve(data)
            },
            onError(reason: any) {
                console.log(reason)
                reject(reason)
            }
        });
    })
}
export const refinanceLoan = (loanId: number, loanData: Record<string, any>) => {
    return new Promise((resolve, reject) => {
        return router.post(`/loans/${loanId}/refinance`, {
            ...loanData,
        }, {
            onSuccess(data: any) {
                resolve(data)
            },
            onError(reason: any) {
                console.log(reason)
                reject(reason)
            }
        });
    })
}
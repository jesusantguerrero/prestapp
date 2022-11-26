// @ts-ignore: unexported from inertia
import { router } from '@inertiajs/vue3';
import { ILoan, ILoanInstallment } from "../loanEntity";

export const createLoan = (loanData: ILoan, installments: ILoanInstallment[]) => {
    return new Promise((resolve, reject) => {
        return router.post('/loans', {
            ...loanData,
            installments
        }, {
            onSuccess(data) {
                resolve(data)
            },
            onError(reason) {
                reject(reason)
            }
        });
    })
}
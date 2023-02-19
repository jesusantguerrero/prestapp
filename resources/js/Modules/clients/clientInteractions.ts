import { IClient } from './clientEntity';
// @ts-ignore: unexported from inertia
import { router } from "@inertiajs/vue3";

class ClientInteractions {
    isGeneratingDistribution = false
    create(clientData: IClient, type: string = 'lender') {
        const formData = {
            ...clientData,
            [`is_${type}`]: true,
        }
        return new Promise((resolve, reject) => {
            router.post('/clients', formData, {
                onSuccess(data: IClient) {
                    resolve(data)
                },
                onError(reason: String) {
                    reject(reason)
                }
            });
        })
    }

    generateOwnerDistribution(ownerId: number, invoiceId?: number) {
      this.isGeneratingDistribution = true;
      const url = `/clients/${ownerId}/owner-distributions`
      if (invoiceId) {
        router.put(`${url}/${invoiceId}`);
      } else {
        router.post(url);
      }
    }
}

export const clientInteractions =  new ClientInteractions()

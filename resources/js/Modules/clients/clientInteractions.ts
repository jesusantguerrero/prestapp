// @ts-ignore: unexported from inertia
import { router } from "@inertiajs/vue3";
import { IClient } from "./clientEntity";

class ClientInteractions {
    isGeneratingDistribution = false
    create(clientData: IClient) {
        return new Promise((resolve, reject) => {
            return router.post('/clients', clientData, {
                onSuccess(data) {
                    resolve(data)
                },
                onError(reason) {
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

import { IClient } from './clientEntity';
// @ts-ignore: unexported from inertia
import { router } from "@inertiajs/vue3";
import { reactive, toRefs } from "vue";

export const InteractionsState = reactive({
  isGeneratingDistribution: false
})
class ClientInteractions {
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
      if (!invoiceId) {
        router.visit(`/owners/draws?filters[owner]=${ownerId}`)
      } else {
        this.generateAutoOwnerDistribution(ownerId, invoiceId)
      }
    }

    generateAutoOwnerDistribution(ownerId: number, invoiceId?: number) {
      InteractionsState.isGeneratingDistribution = true;
      const url = `/clients/${ownerId}/owner-distributions`
      if (invoiceId) {
        router.put(`${url}/${invoiceId}`);
      } else {
        router.post(url);
      }
    }
}

export const clientInteractions =  new ClientInteractions()

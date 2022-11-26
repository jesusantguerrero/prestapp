import { router } from "@inertiajs/vue3";
import { IClient } from "./clientEntity";

class ClientInteractor {
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
}

export const clientInteractor =  new ClientInteractor()

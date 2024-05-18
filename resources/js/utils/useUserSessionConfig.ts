import { Ref, computed, watch } from "vue";
import { useLocalStorage } from "@vueuse/core"

interface SessionConfig {
    filters: Record<string, string>,
}

type UserSessionConfig = Record<string, SessionConfig>

const TOKEN = 'LCH_SESSION:'
export const removeSessionSearch = (sessionToken: string) => {
    localStorage?.removeItem(TOKEN+sessionToken)
}
export const useUserSessionConfig = (
    sessionKey: string,
    subKey: Ref,
    initialConfig = {},
    token = TOKEN
) => {
    //  lets store the visibility configuration in local storage
    const userStoredSearch = useLocalStorage<UserSessionConfig>(token+sessionKey, {});

    const searchFilters = computed({
        get() {
            return userStoredSearch.value[subKey.value]?.filters;
        },
        set(value)  {
            if (!userStoredSearch.value[subKey.value]) {
                userStoredSearch.value[subKey.value] = {
                    filters: initialConfig
                }
            }
            userStoredSearch.value[subKey.value].filters = value
        },
    })

    watch(subKey, () => {
        if (!userStoredSearch.value[subKey.value]) {
            userStoredSearch.value[subKey.value] = {
                filters: initialConfig
            }
        }
    }, {
        immediate: true
    })

    return {
        searchFilters
    }
};

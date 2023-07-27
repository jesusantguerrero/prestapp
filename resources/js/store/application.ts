import { defineStore } from "pinia";
import { useLocalStorage } from "@vueuse/core"
import { computed } from "vue";
export const useApplicationStore = defineStore('application', () => {

    const applicationData = useLocalStorage('applicationData', {
      user: null,
      theme: {

      }
    })

    const isTeamApproved = computed(() => {
      return applicationData.value.isTeamApproved
    })

    const setApplicationData = (data: any) => {
      if (data.application?.teamSettings) {
        applicationData.value = {
          user: data.user,
          isAdmin: data.application.isAdmin,
          isTeamApproved: data.application.isTeamApproved,
          teamSettings: data.application.teamSettings,
          userSettings: data.application.userSettings,
          theme: data.application.team,
          permissions: data.application.permissions,
          userPermissions: data.application.userPermissions
        }
      }
    }

    return {
        setApplicationData,
        applicationData,
        isTeamApproved,
    }
})

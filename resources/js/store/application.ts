import { defineStore } from "pinia";
import {useLocalStorage } from "@vueuse/core"
import { computed } from "vue";
import { hexToRgb } from "@/Pages/Journal/Invoices/utils";
export const useApplicationStore = defineStore('application', () => {

    const applicationData = useLocalStorage('applicationData', {
      user: null,
      theme: {

      },
      isAdmin: false,
      isTeamApproved: false,
      teamSettings: {},
      userSettings: {},
      permissions: {},
      userPermissions: {}
    })

    const isTeamApproved = computed(() => {
      return applicationData.value.isTeamApproved
    })



    const setTheme = () => {
      document.documentElement.style.setProperty('--ic-primary-color', hexToRgb(applicationData.value.theme?.values?.primary));
      document.documentElement.style.setProperty('--ic-secondary-color', hexToRgb(applicationData.value.theme?.values?.secondary));
    }

    const setApplicationData = (data: any) => {
      if (data.application?.teamSettings) {
        applicationData.value = {
          user: data.user,
          isAdmin: data.application.isAdmin,
          isTeamApproved: data.application.isTeamApproved,
          teamSettings: data.application.teamSettings,
          userSettings: data.application.userSettings,
          theme: data.application.theme,
          permissions: data.application.permissions,
          userPermissions: data.application.userPermissions
        }
      }
    }

    return {
        setApplicationData,
        setTheme,
        applicationData,
        isTeamApproved,
    }
})

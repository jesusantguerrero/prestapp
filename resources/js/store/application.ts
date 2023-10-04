import { defineStore } from "pinia";
import {useLocalStorage } from "@vueuse/core"
import { computed } from "vue";
import { hexToRgb } from "@/Pages/Journal/Invoices/utils";

const blueLight = {
  primary: "#47A9F1",
  "primary-light": "#63D0DD",
  "primary-shade-1": "#47A9F1",
  "primary-shade-2": "#47A9F1",
  "primary-shade-3": "#47A9F1",
  "primary-shade-4": "#47A9F1",
  // "secondary": "#7069DE",
  "secondary": "#0C165B",
  "accent": "#5F47DD",
}
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
      return applicationData?.value.isTeamApproved
    })

    const setDefaultTheme = () => {
        document.documentElement.style.setProperty('--ic-primary-color', hexToRgb(blueLight.primary));
        document.documentElement.style.setProperty('--ic-secondary-color', hexToRgb(blueLight.secondary));
    }
    const setTheme = () => {
      const theme = applicationData.value.theme
      try {
        if (theme) {
          document.documentElement.style.setProperty('--ic-primary-color', hexToRgb(applicationData.value.theme?.values?.primary));
          document.documentElement.style.setProperty('--ic-secondary-color', hexToRgb(applicationData.value.theme?.values?.secondary));
        } else {
         setDefaultTheme()
        }
      } catch(err) {
        setDefaultTheme()
      }
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

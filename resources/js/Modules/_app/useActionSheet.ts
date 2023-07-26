import { computed, reactive, toRefs } from "vue"

interface IModalState {
  isOpen: boolean;
  data: null|Record<string, any>;
}
export const modalState = reactive<Record<string, IModalState>>({})

/**
 * useTransactionModal - get controls and state of transaction modal
 * @returns {{ toggleModal: Function, openModal: Function, closeModal: Function, isOpen: Boolean }}
 */
export const useActionSheet = (modalKey = "global") => {
    if (!modalState[modalKey]) {
      modalState[modalKey] = {
        isOpen: false,
        data: null,
      }
    }
    const closeAction = () => {
        modalState[modalKey].isOpen = false
        modalState[modalKey].data = null
    }

    const openAction = (config: IModalState = { data: null, isOpen: true }) => {
        modalState[modalKey].data = config.data ?? null
        modalState[modalKey].isOpen = true
    }

    const toggleAction = (config?: IModalState) => {
        if (modalState[modalKey].isOpen)  {
            closeAction()
        } else {
            openAction(config)
        }
    }

    const { isOpen } = toRefs(modalState[modalKey])

    const data = computed(() => modalState[modalKey].data ?? null)

    return {
        toggleAction,
        openAction,
        closeAction,
        isOpen,
        data
    }
}

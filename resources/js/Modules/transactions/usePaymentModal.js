import { reactive, toRefs } from "vue"

/**
 *
 */
export const modalState = reactive({
    isOpen: false,
    data: null,
    mode: 'EXPENSE',
})

/**
 * useTransactionModal - get controls and state of transaction modal
 * @returns {{ toggleModal: Function, openModal: Function, closeModal: Function, isOpen: Boolean }}
 */
export const usePaymentModal = () => {
    const closeModal = () => {
        modalState.isOpen = false
        modalState.data = null
        modalState.mode = 'EXPENSE'
    }

    const openModal = (config = {}) => {
        modalState.data = config.data ?? null
        modalState.mode = config.mode ?? 'EXPENSE'
        modalState.isOpen = true
    }

    const toggleModal = (config) => {
        if (modalState.isOpen)  {
            closeModal()
        } else {
            openModal(config)
        }
    }

    const { isOpen } = toRefs(modalState)

    return {
        toggleModal,
        openModal,
        closeModal,
        isOpen,
    }
}

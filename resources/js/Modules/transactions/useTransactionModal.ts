import { reactive, toRefs } from "vue"

/**
 *
 */
export const transactionModalState = reactive({
    isOpen: false,
    transactionData: null,
    mode: 'EXPENSE',
    recurrence: false,
    automatic: false,
    hideTypeSelector: false,
})


type TransactionConfig = Record<string, any>;
/**
 * useTransactionModal - get controls and state of transaction modal
 * @returns {{ toggleTransactionModal: Function, openTransactionModal: Function, closeTransactionModal: Function, isOpen: Boolean }}
 */
export const useTransactionModal = () => {
    const closeTransactionModal = () => {
        transactionModalState.isOpen = false
        transactionModalState.automatic = false
        transactionModalState.transactionData = null
        transactionModalState.mode = 'EXPENSE'
        transactionModalState.hideTypeSelector = false
        transactionModalState.recurrence = false
    }

    const openTransactionModal = (config: TransactionConfig = {}) => {
        transactionModalState.automatic = config.automatic ?? false
        transactionModalState.transactionData = config.transactionData ?? null
        transactionModalState.recurrence = config.recurrence ?? false
        transactionModalState.mode = config.mode ?? 'EXPENSE'
        transactionModalState.hideTypeSelector = config.hideTypeSelector ?? false
        transactionModalState.isOpen = true
    }

    const toggleTransactionModal = (config: TransactionConfig) => {
        if (transactionModalState.isOpen)  {
            closeTransactionModal()
        } else {
            openTransactionModal(config)
        }
    }

    const { isOpen } = toRefs(transactionModalState)

    return {
        toggleTransactionModal,
        openTransactionModal,
        closeTransactionModal,
        isOpen,
    }
}

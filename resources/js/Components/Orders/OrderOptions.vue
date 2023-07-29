<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import { ref, computed } from "vue";

import { IInvoice } from "@/Modules/invoicing/entities";
import { ElMessageBox } from "element-plus";
import { formatMoney } from "@/utils";
import { useResponsive } from "@/utils/useResponsive";
import { useActionSheet } from "@/Modules/_app/useActionSheet";

const props = withDefaults(
  defineProps<{
    order: IInvoice;
    accountsEndpoint?: string;
    allowEdit: boolean;
    externalActions?: Record<string, any>;
  }>(),
  {
    accountsEndpoint: "/api/accounts",
    allowEdit: false,
  }
);

defineEmits(["edit"]);

const executeAction = (actionName: string) => (order: IInvoice) => {
  router.post(
    `/orders/${order.id}/actions/${actionName}`,
    {},
    {
      onSuccess() {
        router.reload({
          preserveState: true,
          preserveScroll: true,
        });
      },
    }
  );
};

const actions = computed(() => {
  return {
    payment: {
      label: "Send",
      action: executeAction("send"),
    },
    send: {
      label: "Return",
      action: executeAction("return"),
    },
    download: {
      label: "Mark as received",
      action: executeAction("mark-as-received"),
    },
    ...(props.allowEdit
      ? {
          edit: {
            label: "Edit invoice",
            condition: props.allowEdit,
          },
        }
      : {}),
    view: {
      label: "Cancel",
      action: executeAction("cancel"),
    },
    delete: {
      label: "Delete order",
      action: async (order: IInvoice) => {
        const isValid = await ElMessageBox.confirm(
          `Estas seguro de eliminar la factura ${order.concept} por ${formatMoney(
            order.total
          )}?`,
          "Eliminar factura"
        );

        if (isValid) {
          router.delete(`/invoices/${order.id}`, {
            onSuccess() {
              router.reload({
                preserveState: true,
                preserveScroll: true,
              });
            },
          });
        }
      },
    },
  };
});

const linkToPrint = ref("");
const invoiceLink = ref();

const { isMobile } = useResponsive();
const { openAction } = useActionSheet();
const handleActions = (actionName: string) => {
  const externalActions: Record<string, Function> = props.externalActions;

  if (externalActions?.[actionName]) {
    externalActions[actionName]?.(props.order);
  } else {
    actions.value[actionName]?.action(props.order);
  }
};

const createBasic = () => {
  openAction({
    data: {
      actions: actions,
      title: "Order options",
      onAction: (actionName: string) => {
        handleActions(actionName);
      },
    },
    isOpen: true,
  });
};
</script>

<template>
  <ElDropdown v-if="actions && !isMobile" @command="handleActions($event)">
    <button class="px-5 py-2 rounded-md hover:bg-base-lvl-2">
      <i class="fa fa-ellipsis-h" />
    </button>
    <template #dropdown>
      <ElDropdownMenu>
        <ElDropdownItem :command="actionName" v-for="(action, actionName) in actions">
          {{ $t(action?.label) }}
        </ElDropdownItem>
      </ElDropdownMenu>
    </template>
  </ElDropdown>
  <button
    class="px-5 py-2 rounded-md hover:bg-base-lvl-2"
    v-else-if="actions"
    @click="createBasic"
  >
    <i class="fa fa-ellipsis-h" />
  </button>

  <a :href="linkToPrint" target="_blank" ref="invoiceLink" type="hidden"></a>
</template>

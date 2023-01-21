<template>
  <app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 h-auto py-12">
      <div
        class="flex justify-between items-center mb-10 border-4 border-white bg-gray-50 rounded-md py-2"
      >
        <div class="px-5 text-gray-600 font-bold">Settings / Goals</div>
      </div>

      <div class="divide-y divide-gray-200 space-y-5">
        <div class="pb-2">
          <h2 class="font-bold mb-5 text-lg">Current Goal</h2>

          <div class="bg-white shadow-md rounded-md py-8 px-5 flex w-full justify-around">
            <div class="goal_left w-44">
              <p>Amount Invoiced</p>
              <div>0.00</div>
            </div>

            <div class="goal_center w-full">
              <div class="goal_description">0% of $1,000.00 goal</div>
              <div class="goal_progress"></div>
              <div class="goal_footer">05/13/2021 to 06/12/2021 · 0 invoices sent</div>
            </div>
            <div class="goal_right w-36">
              <div class="goal_days_left">29 Days Left</div>
              <div class="goal_action">
                <at-button class="bg-green-400 text-white"> New Invoice </at-button>
              </div>
            </div>
          </div>
        </div>

        <div class="w-full">
          <div class="md:max-w-sm">
            <h2 class="font-bold my-4 text-lg">Past Goals</h2>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import { ElSwitch } from "element-plus";
import { reactive, toRefs } from "vue";
import { AtTextarea } from "atmosphere-ui";

import AppLayout from "../../Layouts/AppLayout.vue";
import AtInput from "@/Atmosphere/Atoms/Input.vue";
import AtButton from "@/Atmosphere/Atoms/Button.vue";
import SimpleSelect from "@/Atmosphere/Molecules/SimpleSelect.vue";

export default {
  components: {
    AppLayout,
    AtInput,
    AtButton,
    SimpleSelect,
    CustomText,
    ElSwitch,
  },
  setup() {
    const state = reactive({
      formData: {
        invoice_email_text: "",
        invoice_payment_instructions: "",
        invoice_label: "",
        invoice_payment_due_days: 1,
        invoice_accept_online_payments: false,
      },
    });

    return {
      ...toRefs(state),
    };
  },
};
</script>

<style>
.el-switch {
  background: transparent !important;
  border: none !important;
}
</style>

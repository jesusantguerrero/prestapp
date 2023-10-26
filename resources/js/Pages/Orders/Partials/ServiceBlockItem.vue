<script setup lang="ts">
import { formatMoney } from "@/utils";
// @ts-expect-error: no definitions
import { AtField, AtInput } from "atmosphere-ui";
import axios from "axios";
import { computed, watch, ref, watchEffect } from "vue";
import { useI18n } from "vue-i18n";


const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
  disabled: {
    type: Boolean,
    required: false,
  },
  hideFields: {
    type: Array,
    default() {
      return [];
    },
  },
  labels: {
    type: Object,
  },
});

const { t } = useI18n();

const defaultLabels = {
  name: t("service name"),
  price: t("price"),
  qty: t("qty"),
  total: t("total"),
};

const emit = defineEmits(["set-item", "update:item"]);

const labels = computed(() => {
  return Object.assign(defaultLabels, props.labels);
});

const isFieldHidden = (name: string) => {
  return props.hideFields.includes(name);
};

const getLabel = (name: string) => {
  return labels.value[name] || name;
};

const fetchVendorProduct = (productUrl: string) => {
  return axios.get(`/dropshipping/vendor-products/shein?search=${productUrl}`);
};

const isFetching = ref(false);
watch(
  () => props.item.concept,
  async (concept) => {
    if (concept.includes("https://")) {
      isFetching.value = true;
      fetchVendorProduct(concept)
        .then(({ data }) => {
          emit("set-item", {
            concept: data.productName,
            id: data.id,
            price: data.price,
            product_image: data.image,
          });
        })
        .finally(() => {
          isFetching.value = false;
        });
    }
  }
);

watchEffect(async () => {
  emit("update:item", {
    ...props.item,
    total: props.item.price * props.item.qty,
  });
});
</script>

<template>
  <div
    class="w-full text-gray-600 rounded-md form-field"
    :class="[`item-`, disabled ? 'pb-4' : 'bg-white']"
  >
    <div class="flex space-x-4">
      <AtField class="w-6/12" :label="getLabel('name')">
        <section class="flex">
          <div
            class="flex items-center justify-center mr-4 overflow-hidden font-bold text-gray-400 border border-gray-300 rounded-md h-28 w-28 bg-gray-50"
          >
            <img
              :src="item.product_image"
              alt=""
              v-if="item.product_image?.length"
              style="
                min-width: 100%;
                min-height: 100%;
                object-fit: cover;
                object-position: top;
              "
            />
            <i class="text-xl fa fa-images" v-else></i>
          </div>
          <section class="w-full">
            <div>
              <AtInput
                v-model="item.concept"
                :placeholder="$t('copy and paste the shein url here')"

                :disabled="disabled || isFetching"
              >
                <template #suffix v-if="isFetching">
                  <IMdiSync class="animate-spin" />
                </template>
              </AtInput>
            </div>
            <textarea
              v-model="item.description"
              v-if="!isFieldHidden('description') && !disabled && item.showDescription"
              class="w-full px-2 py-1 transition border-gray-200 shadow-sm resize-none focus:outline-none focus:border-gray-400"
              :class="[disabled ? 'border-0 mt-2 px-0' : 'border mt-4']"
              placeholder="Enter description here"
              :disabled="disabled"
            />
            <section>
              <span class="inline-block mt-4 font-bold">

                {{ formatMoney(item.price * item.quantity) }}
              </span>
            </section>
          </section>
        </section>
      </AtField>
      <div class="flex w-6/12 space-x-2">
        <AtField class="w-full" v-if="!disabled" :label="getLabel('price')">
          <AtInput v-model="item.price" number-format :disabled="disabled" />
        </AtField>
        <AtField
          class="w-full"
          v-if="!isFieldHidden('description') && !disabled"
          :label="getLabel('qty')"
        >
          <AtInput v-model="item.quantity" :disabled="disabled" />
        </AtField>
      </div>
    </div>
  </div>
</template>

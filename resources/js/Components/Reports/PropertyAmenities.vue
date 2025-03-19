<script setup lang="ts">
import { useI18n } from "vue-i18n";

const { t } = useI18n();

interface Props {
  amenities: Record<string, number>;
}

defineProps<Props>();
</script>

<template>
  <div class="mb-4" v-if="Object.keys(amenities).length">
    <h3 class="text-sm font-semibold text-gray-700 mb-2">{{ t('Amenities') }}</h3>
    <div class="flex flex-wrap gap-2">
      <div v-for="(count, amenity) in amenities" :key="amenity" 
           class="px-2 py-1 bg-slate-100 rounded-full text-sm">
        {{ amenity }} ({{ count }})
      </div>
    </div>
  </div>
</template> 
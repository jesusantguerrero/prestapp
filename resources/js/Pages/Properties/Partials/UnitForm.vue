<script setup lang="ts">
import { AtInput, AtTextarea } from "atmosphere-ui";
import { ref } from "vue";
import AppFormField from "@/Components/shared/AppFormField.vue";

import { IUnit } from "@/Modules/properties/propertyEntity";
import { ElDialog, ElUpload, UploadProps } from "element-plus";

const props = defineProps<{
  unit: IUnit;
}>();

const setImages = (imageObject) => {
  props.unit.images.push(imageObject);
};

const dialogImageUrl = ref("");
const dialogVisible = ref(false);

const handleRemove: UploadProps["onRemove"] = (uploadFile, uploadFiles) => {
  console.log(uploadFile, uploadFiles);
};

const handlePictureCardPreview: UploadProps["onPreview"] = (uploadFile) => {
  dialogImageUrl.value = uploadFile.url!;
  dialogVisible.value = true;
};
</script>

<template>
  <section>
    <section class="grid grid-cols-4 gap-4">
      <AppFormField class="w-full" label="Precio de Renta">
        <AtInput v-model="unit.price" class="w-full" rounded number-format />
      </AppFormField>
      <AppFormField class="w-full" label="Area" v-model="unit.area" rounded>
        <template #prefix>
          <span class="inline-blocks h-full flex items-center px-2">
            <i-ic-sharp-photo-size-select-small />
          </span>
        </template>
      </AppFormField>
      <AppFormField class="w-full" :label="$t('rooms')" v-model="unit.bedrooms" rounded>
        <template #prefix>
          <span class="inline-blocks h-full flex items-center px-2">
            <IIcTwotoneBed />
          </span>
        </template>
      </AppFormField>
      <AppFormField
        class="w-full"
        label="Baños"
        v-model="unit.bathrooms"
        rounded
        placeholder="0"
      >
        <template #prefix>
          <span class="inline-blocks h-full flex items-center px-2">
            <IIcTwotoneBathtub />
          </span>
        </template>
      </AppFormField>
    </section>
    <AppFormField :label="$t('description')">
      <AtTextarea
        v-model="unit.description"
        class="w-full p-2 border focus:outline-none"
        :placeholder="$t('Notes, description, details...')"
      />
    </AppFormField>
    <AppFormField :label="$t('images')">
      <ElUpload
        v-model:file-list="unit.images"
        :accept="'images'"
        :auto-upload="false"
        :list-type="'picture-card'"
        :multiple="true"
        :on-preview="handlePictureCardPreview"
        :on-remove="handleRemove"
      />
    </AppFormField>

    <ElDialog v-model="dialogVisible">
      <img w-full :src="dialogImageUrl" alt="Preview Image" />
    </ElDialog>
  </section>
</template>

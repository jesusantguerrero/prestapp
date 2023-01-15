import { cloneDeep } from "lodash";
import { Ref, reactive, watch } from "vue";

export const useReactiveForm = (formData: Record<string, any>, modelValue: Ref, emit: (name: string, data: any) => {}) => {

  const form = reactive(cloneDeep(formData));

  watch(
    () => modelValue.value,
    (data) => {
      console.log(data)
      Object.keys(form).map((field) => {
        if (data[field]) {
          form[field] = data[field];
        }
      });
    },
    { immediate: true, deep: true }
  );
  
  watch(
    () => form,
    (data) => {
      emit("update:modelValue", {
        ...modelValue.value, 
        ...cloneDeep(data),
      });
    }, {
      deep: true,
    }
  );

  return {
    formData: form,
  }
}
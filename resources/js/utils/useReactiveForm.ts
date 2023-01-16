import { cloneDeep } from "lodash";
import { Ref, reactive, watch } from "vue";

export const useReactiveForm = (formData: Record<string, any>, modelValue: Ref, emit: (name: string, data: any) => {}) => {
  const form = reactive(formData);

  watch(
    () => modelValue.value,
    (data) => {
      Object.keys(form).map((field) => {
        if (data[field] && data[field] !== form[field]) {
          form[field] = data[field] || form[field];
          console.log("applied")
        }
        console.log("back but not applied")
      });
    },
    { immediate: true, deep: true }
  );
  
  watch(
    () => { return {...form}},
    () => {
      console.log("here are we?")
      emit("update:modelValue", {
        ...modelValue.value, 
        ...form,
      });
    }, {
      deep: true
    }
  );

  return {
    formData: form,
  }
}
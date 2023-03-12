import { Ref, reactive, watch } from "vue";

export const useReactiveForm = (formData: Record<string, any>, modelValue: Ref, emit: (name: string, data: any) => {}) => {
  const form = reactive(formData);

  watch(
    () => modelValue.value,
    (data) => {
      Object.keys(form).map((field) => {
        if (data[field] && data[field] !== form[field]) {
          form[field] = data[field] || form[field];
        }
      });
    },
    { immediate: true, deep: true }
  );

  watch(
    () => { return {...form}},
    () => {
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

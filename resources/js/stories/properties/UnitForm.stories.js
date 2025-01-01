import UnitForm from '../../Pages/Properties/Partials/UnitForm.vue';

// More on how to set up stories at: https://storybook.js.org/docs/vue/writing-stories/introduction
export default {
  title: 'Properties/UnitForm',
  component: UnitForm,
  tags: ['autodocs'],
  render: (args) => ({
    components: {
      UnitForm,
    },
    setup() {
      return {
        ...args,
      };
    },
    template: '<UnitForm :unit="unit" />',
  }),
  parameters: {
    layout: 'fullscreen',
  },
};

// More on writing stories with args: https://storybook.js.org/docs/vue/writing-stories/args
export const Update = {
  args: {
    unit: {},
  },
};

export const Create = {
  args: {
    unit: {

    },
  },
};

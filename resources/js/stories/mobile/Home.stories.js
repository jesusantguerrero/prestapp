import MobileMenuBar from '../../Components/mobile/MobileMenuBar.vue';

// More on how to set up stories at: https://storybook.js.org/docs/vue/writing-stories/introduction
export default {
  title: 'Mobile/Home',
  component: MobileMenuBar,
  tags: ['autodocs'],
  argTypes: {
    backgroundColor: {
      control: 'color',
    },
    onClick: {},
    size: {
      control: {
        type: 'select',
      },
      options: ['small', 'medium', 'large'],
    },
  },
};

// More on writing stories with args: https://storybook.js.org/docs/vue/writing-stories/args
export const Home = {
  args: {
    primary: true,
    label: 'Button',
  },
};

export const Commissions = {
  args: {
    label: 'Button',
  },
};

export const RentRoll = {
  args: {
    size: 'large',
    label: 'Button',
  },
};

export const Properties = {
  args: {
    size: 'small',
    label: 'Button',
  },
};

const config: StorybookConfig = {
  stories: ["../resources/js/stories/**/*.mdx", "../resources/js/stories/**/*.stories.@(js|jsx|ts|tsx)"],
  addons: [
    "@storybook/addon-links",
    "@storybook/addon-essentials",
    "@storybook/addon-interactions",
    "@storybook/addon-mdx-gfm",
    "@chromatic-com/storybook"
  ],
  framework: {
    name: "@storybook/vue3-vite",
    options: {}
  },
  docs: {}
};

export default config;

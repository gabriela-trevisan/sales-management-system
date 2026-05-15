import type { Preview, Decorator } from '@storybook/react-vite';
import '../src/index.css';

/** Aplica a classe .dark no html quando o background-theme do Storybook é 'dark' */
const withTheme: Decorator = (Story, context) => {
  const isDark = context.globals?.backgrounds?.value === '#1a1a2e';
  document.documentElement.classList.toggle('dark', isDark);
  return <Story />;
};

const preview: Preview = {
  decorators: [withTheme],
  globalTypes: {
    backgrounds: {
      defaultValue: { name: 'Light', value: '#f7f7fb' },
    },
  },
  parameters: {
    backgrounds: {
      default: 'Light',
      values: [
        { name: 'Light', value: '#f7f7fb' },
        { name: 'Dark',  value: '#1a1a2e' },
      ],
    },
    controls: {
      matchers: {
        color: /(background|color)$/i,
        date: /Date$/i,
      },
    },
    a11y: {
      test: 'todo',
    },
  },
};

export default preview;
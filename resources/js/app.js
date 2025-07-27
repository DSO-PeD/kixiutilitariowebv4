 import './bootstrap';
import '../css/app.css';
import { createApp, h } from 'vue';
import { createInertiaApp, Head, Link, router } from '@inertiajs/vue3';
import Layout from './Pages/Layouts/AppLayout.vue';

// 1. Pré-carregamento de componentes críticos
const preloadedComponents = import.meta.glob([
  './Pages/Dashboard.vue',
  './Pages/Auth/Login.vue',
  './Pages/Layouts/**/*.vue'
], { eager: true });

// 2. Configuração de code splitting
const lazyLoadPage = (name) => {
  return () => import(`./Pages/${name}.vue`);
};
// Configuração do pré-carregamento
const setupPreloading = () => {
  // Pré-carrega os links quando o mouse está sobre eles
  document.addEventListener('mouseover', (event) => {
    const link = event.target.closest('a[href]');
    if (link && link.href.startsWith(window.location.origin)) {
      router.preload(link.href);
    }
  }, { capture: true });
};
createInertiaApp({
  title: (title) => `KXU - ${title}`,

  resolve: async (name) => {
    // 3. Verifica primeiro os componentes pré-carregados
    const preloadedKey = `./Pages/${name}.vue`;
    if (preloadedComponents[preloadedKey]) {
      const page = preloadedComponents[preloadedKey];
      page.default.layout = page.default.layout || Layout;
      return page;
    }

    // 4. Carregamento lazy para outras páginas
    const page = await lazyLoadPage(name)();
    page.default.layout = page.default.layout || Layout;
    return page;
  },

  setup({ el, App, props, plugin }) {
    // 5. Criação otimizada da app Vue
    const app = createApp({
      render: () => h(App, props)
    });

    app.use(plugin)
       .component('Head', Head)
       .component('Link', Link);

    // 6. Adiar montagem até que tudo esteja pronto
    requestIdleCallback(() => {
      app.mount(el);
    });
      setupPreloading(); // Chama a função de pré-carregamento aqui
  },

  progress: {
    delay: 100,  // Reduzido de 250ms para 100ms
    color: '#f89d1b',
    includeCSS: true,
    showSpinner: false,  // Desligado para melhor percepção de performance
  },
});

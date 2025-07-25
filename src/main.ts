import { createApp } from 'vue';
import '@unocss/reset/tailwind.css';
import App from './App.vue';
import router from './router/index';
import 'uno.css';
import './style.css';

const app = createApp(App);

app.use(router);

app.mount('#app');

import {createApp} from 'vue';
import App from './App.vue';
import router from './router';
import 'uno.css'; // Import UnoCSS

const app = createApp(App);

app.use(router);

app.mount('#app');

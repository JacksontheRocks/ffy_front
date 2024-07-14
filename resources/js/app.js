import './bootstrap';
import { createApp } from 'vue';
import HomeMapComponent from './components/HomeMapComponent.vue';
import AdminSettings from './components/AdminSettings.vue';

const app = createApp({});
app.component('home-map-component', HomeMapComponent);
app.component('admin-settings', AdminSettings);
app.mount('#app');

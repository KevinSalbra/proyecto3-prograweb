import './bootstrap';
import 'primeflex/primeflex.css';
import 'primeicons/primeicons.css';
import './styles/primevue-overrides.css';

import { createApp } from 'vue';
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';

import Button from 'primevue/button';
import Card from 'primevue/card';
import Checkbox from 'primevue/checkbox';
import Column from 'primevue/column';
import ConfirmDialog from 'primevue/confirmdialog';
import DataTable from 'primevue/datatable';
import FileUpload from 'primevue/fileupload';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import Message from 'primevue/message';
import Paginator from 'primevue/paginator';
import Select from 'primevue/select';
import Password from 'primevue/password';
import Textarea from 'primevue/textarea';
import Toast from 'primevue/toast';

import router from './router';
import App from './App.vue';

const app = createApp(App);

app.use(router);
app.use(PrimeVue, {
    ripple: true,
    inputVariant: 'outlined',
    theme: {
        preset: Aura,
    },
});
app.use(ConfirmationService);
app.use(ToastService);

app.component('Button', Button);
app.component('Card', Card);
app.component('Checkbox', Checkbox);
app.component('Column', Column);
app.component('ConfirmDialog', ConfirmDialog);
app.component('DataTable', DataTable);
app.component('FileUpload', FileUpload);
app.component('InputNumber', InputNumber);
app.component('InputText', InputText);
app.component('Message', Message);
app.component('Paginator', Paginator);
app.component('Select', Select);
app.component('Password', Password);
app.component('Textarea', Textarea);
app.component('Toast', Toast);

app.mount('#app');

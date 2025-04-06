/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import $ from 'jquery';
window.$ = window.jQuery = $;
import { createApp } from 'vue';
import Container from './Container.vue';
import DataTable from './components/DataTable/DataTable.vue';
import HelpMessage from './components/Form/HelpMessage.vue';
import { createRouter, createWebHistory } from 'vue-router';
import { routes } from './router/router'
import { hasPermission } from './authorization'
import Notifications from 'vue3-vt-notifications'
import PrimeVue, { defaultOptions } from 'primevue/config';
import InputText from 'primevue/inputtext';
import FloatLabel from 'primevue/floatlabel';
import FileUpload from 'primevue/fileupload';
import Panel from 'primevue/panel';
import Calendar from 'primevue/calendar';
import Dropdown from 'primevue/dropdown';
import InputNumber from 'primevue/inputnumber';
import Card from 'primevue/card';
import Button from 'primevue/button';
import ButtonGroup from 'primevue/buttongroup';
import Textarea from 'primevue/textarea';
import MultiSelect from 'primevue/multiselect';
import Editor from 'primevue/editor';
import Dialog from 'primevue/dialog';
import Checkbox from 'primevue/checkbox';
import RadioButton from 'primevue/radiobutton';
import ProgressSpinner from 'primevue/progressspinner';
import AutoComplete from 'primevue/autocomplete';
import ConfirmationService from 'primevue/confirmationservice';
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';
import InputSwitch from 'primevue/inputswitch';
import Stepper from 'primevue/stepper';
import StepperPanel from 'primevue/stepperpanel';
import Vue3Toastify from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import ConfirmDialog from 'primevue/confirmdialog';
import Badge from 'primevue/badge';
import BadgeDirective from 'primevue/badgedirective';
import Tree from 'primevue/tree';
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import Password from 'primevue/password';

import ToastService from 'primevue/toastservice';
import Toast from 'primevue/toast';
import 'tabulator-tables/dist/css/semantic-ui/tabulator_semantic-ui.css';
import { passTrough } from './global.config.js'
import '../css/custom.css'

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */
const router = createRouter({
    history: createWebHistory(),
    routes: routes,
})
const app = createApp(Container)
app.directive('badge', BadgeDirective);
app.component('Badge', Badge)
app.component('Tree', Tree)
app.component('Toast', Toast)
app.component('ConfirmDialog', ConfirmDialog)
app.component('Stepper', Stepper)
app.component('StepperPanel', StepperPanel)
app.component('side-menu', Container)
app.component('DataTable', DataTable)
app.component('FileUpload', FileUpload)
app.component('InputText', InputText)
app.component('FloatLabel', FloatLabel)
app.component('Panel', Panel)
app.component('Calendar', Calendar)
app.component('Dropdown', Dropdown)
app.component('InputNumber', InputNumber)
app.component('Card', Card)
app.component('Button', Button)
app.component('ButtonGroup', ButtonGroup)
app.component('Textarea', Textarea)
app.component('MultiSelect', MultiSelect)
app.component('Editor', Editor)
app.component('HelpMessage', HelpMessage)
app.component('Dialog', Dialog)
app.component('Checkbox', Checkbox)
app.component('RadioButton', RadioButton)
app.component('ProgressSpinner', ProgressSpinner)
app.component('AutoComplete', AutoComplete)
app.component('InputGroup', InputGroup)
app.component('InputGroupAddon', InputGroupAddon)
app.component('InputSwitch', InputSwitch)
app.component('Password', Password)
app.component('TabView', TabView)
app.component('TabPanel', TabPanel)
// import ExampleComponent from './components/ExampleComponent.vue';
// app.component('example-component', ExampleComponent);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */
// Vue.use(VueRouter)

// const router = new VueRouter(routes)

router.beforeEach((to, _, next) => {
    const { code, permission } = to.meta
    // If not yet assign meta
    if (!code || !permission) return next()

    // If has permission
    if (hasPermission(code, permission)) return next()

    return next('/403')

})

app.use(Vue3Toastify, {
    clearOnUrlChange: false,
});
app.use(ConfirmationService);
app.use(router)
app.use(ToastService, {
    defaultOptions: {
        position: 'bottom-right'
    }
});
app.use(PrimeVue, {
    pt: passTrough
});
app.use(Notifications);
app.mount('#app')
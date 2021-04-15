/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
window.Vue = require('vue').default;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

//Vue.component('test', require('./components/ExampleComponent.vue').default);
Vue.component('uikit-header', require('./components/Header.vue').default);
Vue.component('button-active', require('./components/ButtonActive').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import router from "./router";
// styles
//import "./assets/css/uikit.min.css";




// const Pusher = require('pusher');

// const pusher = new Pusher({
//     appId:'1174295',
//     key:'d8e5aa9062b273c29902',
//     secret:'29c904c9349b7b825320',
//     cluster:'eu'
// });

// Vue.use(require('vue-pusher'), {
//     api_key: 'd8e5aa9062b273c29902',
//     options: {
//         cluster: 'eu',
//         encrypted: true,
//     }
// });


// import Echo from 'laravel-echo'
// window.Pusher = require('pusher-js');
// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'd8e5aa9062b273c29902'
// });
// window.Echo.channel('chat-room.1')
//     .listen('App\\Events\\ChatMessageWasReceived', (e) => {
//         console.log(112);
//     });


// Pusher.logToConsole = true;
// window.pusher = new Pusher('d8e5aa9062b273c29902', {
//     cluster: 'eu'
// });




const app = new Vue({
    el: '#app',
    router
});



Vue.filter('truncate', function (text, length, suffix) {
    if (text.length > length) {
        return text.substring(0, length) + suffix;
    } else {
        return text;
    }
});




// console.log(1233);
//
//

//

//

//
// // window.Echo.channel('updates')
// //     .listen('ChatMessageWasReceived',
// //             data => this.updates.unshift(data.update)
// //     );
//
// window.Echo.channel('chat-room.1')
//     .listen('ChatMessageWasReceived', (e) => {
//         console.log(1234);
//     });

// import Echo from "laravel-echo"
//
// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'd8e5aa9062b273c29902'
// });

// import EchoLibrary from "laravel-echo"
//
// window.Echo = new EchoLibrary({
//     broadcaster: 'pusher',
//     key: 'd8e5aa9062b273c29902'
// });
//
//
// console.log(1233);
//
// Echo.channel('my-channel')
//     .listen('my-event', (e) => {
//         console.log(123);
//     });

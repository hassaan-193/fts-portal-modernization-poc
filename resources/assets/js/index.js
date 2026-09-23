// Url retrieval function
window.baseUrl = function(path) {
    let basePath = document.querySelector('meta[name="base-url"]').getAttribute('content');
    if (basePath[basePath.length-1] === '/') basePath = basePath.slice(0, basePath.length-1);
    if (path[0] === '/') path = path.slice(1);
    return basePath + '/' + path;
};

// Set events and http services on window
import Events from "./services/events"
import httpInstance from "./services/http"
const eventManager = new Events();
window.$http = httpInstance;
window.$events = eventManager;


// Make services available to Vue instances
import Vue from "vue"
Vue.prototype.$http = httpInstance;
Vue.prototype.$events = eventManager;

//Loader
import Loading from 'vue-loading-overlay';
Vue.use(Loading , {
    container: null,
    canCancel: false,
});
window.$loading = Loading;

// Load Vues and components
import vues from "./vues/vues"
import components from "./components"
vues();
components();

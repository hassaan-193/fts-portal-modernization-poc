import test from "./test";

const componentMapping = {
    'test' : test
};

window.components = {};

const componentNames = Object.keys(componentMapping);

/**
 * Initialize components of the given name within the given element.
 * @param {String} componentName
 * @param {HTMLElement|Document} parentElement
 */
function initComponent(componentName, parentElement) {
    let elems = parentElement.querySelectorAll(`[${componentName}]`);
    if (elems.length === 0) return;

    let component = componentMapping[componentName];
    if (typeof window.components[componentName] === "undefined") window.components[componentName] = [];
    for (let j = 0, jLen = elems.length; j < jLen; j++) {
        let instance = new component(elems[j]);
        if (typeof elems[j].components === 'undefined') elems[j].components = {};
        elems[j].components[componentName] = instance;
        window.components[componentName].push(instance);
    }
}

/**
 * Initialize all components found within the given element.
 * @param parentElement
 */
function initAll(parentElement) {
    if (typeof parentElement === 'undefined') parentElement = document;
    for (let i = 0, len = componentNames.length; i < len; i++) {
        initComponent(componentNames[i], parentElement);
    }
}

window.components.init = initAll;

export default initAll;

import { defineAsyncComponent } from 'vue';

// Import all components
const components = import.meta.glob('@/Components/**/*.vue');

export default {
    install(app) {
        // Register all components globally
        Object.entries(components).forEach(([path, component]) => {
            // Extract component name from path
            const componentName = path
                .split('/')
                .pop()
                .replace(/\.\w+$/, '');
            
            // Register component globally
            app.component(componentName, defineAsyncComponent(component));
        });
    }
};

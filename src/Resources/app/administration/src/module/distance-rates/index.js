const { Module } = Shopware;
import './page/distance-rates-list'; // Component registered for module which will used for showing data  
import './page/distance-rates-detail';
import './page/distance-rates-create';

import enGB from './snippet/en-GB';
import deDE from './snippet/de-DE';

Module.register('distance-rates', {
    type: 'plugin',
    title: 'Distance Rate Shipping',
    description: 'Manage Rates',
    snippets: {
        'en-GB': enGB,
        'de-De': deDE
    },

    routes: {
        list: {
            component: 'distance-rates-list',
            path: 'list'
        },
        // This is our second route
        detail: {
            component: 'distance-rates-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'distance.rates.list'
            }
        },
        create: {
            component: 'distance-rates-create',
            path: 'create',
            meta: {
                parentPath: 'distance.rates.list'
            }
        }
    },
    navigation: [{
        id: 'rate-manage',
        path: 'distance.rates.list',
        label: 'distanceRate.general.mainMenuItemGeneral',
        icon: 'default-shopping-paper-bag-product',
        parent: 'sw-content',
        position: 100
    }]
});
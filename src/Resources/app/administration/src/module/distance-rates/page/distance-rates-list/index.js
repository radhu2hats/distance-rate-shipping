import template from './distance-rates-list.html.twig';

const { Component } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('distance-rates-list', {
    template,

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            repository: null,
            distanceRates: null
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    computed: {
        columns() {
            return [{
                property: 'title',
                dataIndex: 'title',
                label: this.$t('distanceRate.list.columnTitle'),
                inlineEdit: 'string',
                allowResize: true
            }, {
                property: 'range_from',
                dataIndex: 'range_from',
                label: this.$t('distanceRate.list.columnRangeFrom'),
                allowResize: true
            },{
                property: 'range_to',
                dataIndex: 'range_to',
                label: this.$t('distanceRate.list.columnRangeTo'),
                allowResize: true
            },{
                property: 'price',
                dataIndex: 'price',
                label: this.$t('distanceRate.list.columnPrice'),
                allowResize: true
            },{
                property: 'status',
                dataIndex: 'status',
                label: this.$t('distanceRate.list.activateLabel'),
                allowResize: true
            }];
        }
    },

    created() {
        this.repository = this.repositoryFactory.create('distance_rate');

        this.repository
            .search(new Criteria(), Shopware.Context.api)
            .then((result) => {
                this.distanceRates = result;
                console.log(result);
            });
    }
});
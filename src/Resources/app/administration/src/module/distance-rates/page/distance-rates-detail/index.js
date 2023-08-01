import template from './distance-rates-detail.html.twig';

const { Component, Mixin } = Shopware;

const Criteria = Shopware.Data.Criteria;

Component.register('distance-rates-detail', {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('notification')
    ],

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    data() {
        return {
            distanceRate: null,
            isLoading: false,
            processSuccess: false,
            repository: null,
            
        };
    },

    created() {
        this.repository = this.repositoryFactory.create('distance_rate');
        this.getDistanceRate();
    },
    watch: {
        'distanceRate.status': function () {
            return this.distanceRate.status ? 1 : 0;
        }
    },
    methods: {

        getDistanceRate() {
            this.repository
                .get(this.$route.params.id, Shopware.Context.api)
                .then((entity) => {
                    this.distanceRate = entity;
                });
        },
        
        onClickSave() {
            this.isLoading = true;

            this.distanceRate.range_from = parseInt(this.distanceRate.range_from, 10);
            this.distanceRate.range_to = parseInt(this.distanceRate.range_to, 10);
            this.distanceRate.price = parseFloat(this.distanceRate.price);


            this.repository
                .save(this.distanceRate, Shopware.Context.api)
                .then(() => {
                    this.getDistanceRate();
                    this.isLoading = false;
                    this.processSuccess = true;
                }).catch((exception) => {
                    this.isLoading = false;
                    this.createNotificationError({
                        title: this.$t('distanceRate.detail.errorTitle'),
                        message: exception
                    });
                });
        },

        saveFinish() {
            this.processSuccess = false;
        }
    }
});
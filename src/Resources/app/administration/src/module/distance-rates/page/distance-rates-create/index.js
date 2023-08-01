const { Component } = Shopware;

Component.extend('distance-rates-create', 'distance-rates-detail', {
    methods: {
        getDistanceRate() {
            this.distanceRate = this.repository.create(Shopware.Context.api);
        },

        onClickSave() {
            this.isLoading = true;

            this.repository
                .save(this.distanceRate, Shopware.Context.api)
                .then(() => {
                    this.isLoading = false;
                    this.$router.push({ name: 'distance.rates.detail', params: { id: this.distanceRate.id } });
                }).catch((exception) => {
                    this.isLoading = false;

                    this.createNotificationError({
                        title: this.$t('distanceRate.errorTitle'),
                        message: exception
                    });
                });
        }
    }
});
<template>
	<div class="row my-flex-card">
        <div class="col-md-4 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h1>$ {{ slp_data.price }}</h1>
                            <b :class="slp_data.change_24 > 0 ? 'text-success' : 'text-danger'">
                                <h3>{{ slp_data.change_24 }}%
                                    <template v-if="slp_data.change_24 > 0">
                                        <i class="fas fa-level-up-alt text-success"></i>
                                    </template>
                                    <template v-else>
                                        <i class="fas fa-level-down-alt text-danger"></i>
                                    </template>
                                </h3>
                            </b>
                        </div>
                        <div class="col-md-6">
                            <div class="align-self-center">
                                <img src="../../../../images/SLP.png" style="width : 100px" class="img-fluid float-right" alt="Responsive image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h1>$ {{ axs_data.price }}</h1>
                            <b :class="axs_data.change_24 > 0 ? 'text-success' : 'text-danger'">
                                <h3>{{ axs_data.change_24 }}%
                                    <template v-if="axs_data.change_24 > 0">
                                        <i class="fas fa-level-up-alt text-success"></i>
                                    </template>
                                    <template v-else>
                                        <i class="fas fa-level-down-alt text-danger"></i>
                                    </template>
                                </h3>
                            </b>
                        </div>
                        <div class="col-md-6">
                            <div class="align-self-center">
                                <img src="../../../../images/AXS.png" style="width : 100px" class="img-fluid float-right" alt="Responsive image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h1>$ {{ eth_data.price }}</h1>
                                <b :class="eth_data.change_24 > 0 ? 'text-success' : 'text-danger'">
                                <h3>{{ eth_data.change_24 }}%
                                    <template v-if="eth_data.change_24 > 0">
                                        <i class="fas fa-level-up-alt text-success"></i>
                                    </template>
                                    <template v-else>
                                        <i class="fas fa-level-down-alt text-danger"></i>
                                    </template>
                                </h3>
                            </b>
                        </div>
                        <div class="col-md-6">
                            <div class="align-self-center">
                                <img src="../../../../images/ETH.png" style="width : 100px" class="img-fluid float-right" alt="Responsive image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<loader :loading="loading"></loader>
	</div>
</template>

<script type="text/javascript">
import FetchMixin from 'Mixins/fetch.js';

import Card from 'Components/containers/Card.vue';
import DateRange from 'Components/datepickers/DateRange.vue';
import Charts from 'Components/charts/Chart.vue';
import BoxWidget from 'Components/widgets/BoxWidget.vue';

export default {

    mounted() {
        this.getCoin('smooth-love-potion');
        this.getCoin('ethereum');
        this.getCoin('axie-infinity');

        setInterval(()=> {
            this.getCoin('smooth-love-potion');
            this.getCoin('ethereum');
            this.getCoin('axie-infinity');
        }, 10000)
    },

	methods: {
        getCoin(coin) {
            let instance = axios.create();
            delete instance.defaults.headers.common['X-CSRF-TOKEN'];
            delete instance.defaults.headers.common['X-Requested-With'];

            const data = instance({
                method: 'get',
                url: this.url + coin,
            }).then(response =>{
                return this.parseData(coin, response.data);
            }).catch(error =>{
                console.log(error)
            });
        },
        parseData(coin, data) {
            let price = {
                price : data.market_data.current_price.usd.toFixed(2),
                change_24 : data.market_data.price_change_percentage_24h_in_currency.usd.toFixed(2),
            };

            switch (coin) {
                case 'smooth-love-potion':
                    this.slp_data = price;
                    break;
                case 'ethereum':
                    this.eth_data = price;
                    break;
                case 'axie-infinity':
                    this.axs_data = price;
                    break;
                default:
                    break;
            }
        }
	},

	data() {
		return {
            url : 'https://api.coingecko.com/api/v3/coins/',
            slp_data : {
                price : null,
                change_24 : null,
            },
            axs_data : {
                price : null,
                change_24 : null,
            },
            eth_data : {
                price : null,
                change_24 : null,
            },
		}
	},

	props: {
		title: String,
	},

	computed: {
		fetchParams() {
			return this.filters;
		},
	},

	components: {
		'card': Card,
		'date-range': DateRange,
		'chart': Charts,
		'box-widget': BoxWidget,
	},

	mixins: [ FetchMixin ],
}
</script>


<style type="text/css">
    .grey-bg {
        background-color: #F5F7FA;
    }
</style>

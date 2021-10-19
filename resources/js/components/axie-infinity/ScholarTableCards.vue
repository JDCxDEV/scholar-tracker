<template>
    <div>
        <div class="row">
            <div class="col-sm">
                <div class="info-box mb-3 bg-dark">
                    <span class="info-box-icon"><i class="fa fa-users"></i></span>

                    <div class="info-box-content">
                      <span class="info-box-text">Total Scholars</span>
                      <span class="info-box-number">
                            <template v-if="loading">
                                <i class="fas fa-circle-notch fa-spin"></i>
                            </template>
                            <template v-else>
                                {{ data.scholars }}
                            </template>
                        </span>
                    </div>
                    <!-- /.info-box-content -->
                  </div>
            </div>
            <div class="col-sm">
                <div class="info-box mb-3 bg-dark">
                    <span class="info-box-icon"><img style="max-width: 80%;" src="https://www.pikpng.com/pngl/b/574-5749651_well-if-you-have-any-talents-like-these.png" alt=""></span>

                    <div class="info-box-content">
                      <span class="info-box-text">Total Axies</span>
                      <span class="info-box-number">
                            <template v-if="axie_count_loading">
                                <i class="fas fa-circle-notch fa-spin"></i>
                            </template>
                            <template v-else>
                               {{ axie_count ? axie_count : 0 }}
                            </template>
                      </span>
                    </div>
                    <!-- /.info-box-content -->
                  </div>
            </div>
            <div class="col-sm">
                <div class="info-box mb-3 bg-dark">
                    <span class="info-box-icon"><img style="max-width: 80%;" src="https://assets.coingecko.com/coins/images/10366/small/SLP.png" alt=""></span>

                    <div class="info-box-content">
                      <span class="info-box-text">Today Slp</span>
                      <span class="info-box-number">
                            <template v-if="loading || coin_loading">
                                <i class="fas fa-circle-notch fa-spin"></i>
                            </template>
                            <template v-else>
                               {{ data.today_slp  }} (${{ (data.today_slp * slp_data.price).toFixed(2)}})
                            </template>

                      </span>
                    </div>
                    <!-- /.info-box-content -->
                  </div>
            </div>
            <div class="col-sm">
                <div class="info-box mb-3 bg-dark">
                    <span class="info-box-icon"><img style="max-width: 80%;" src="https://assets.coingecko.com/coins/images/10366/small/SLP.png" alt=""></span>

                    <div class="info-box-content">
                      <span class="info-box-text">Total Slp</span>
                      <span class="info-box-number">
                            <template v-if="loading || coin_loading">
                                <i class="fas fa-circle-notch fa-spin"></i>
                            </template>
                            <template v-else>
                                {{ data.total_slp  }} (${{ (data.total_slp * slp_data.price).toFixed(2)}})
                            </template>
                      </span>
                    </div>
                    <!-- /.info-box-content -->
                  </div>
            </div>
            <div class="col-sm">
                <div class="info-box mb-3 bg-secondary">
                    <span class="info-box-icon"><img style="max-width: 80%;" src="https://assets.coingecko.com/coins/images/10366/small/SLP.png" alt=""></span>

                    <div class="info-box-content">
                      <span class="info-box-text">SLP / USD</span>
                      <span class="info-box-number">
                            <template v-if="coin_loading">
                                <i class="fas fa-circle-notch fa-spin"></i>
                            </template>
                            <template v-else>
                             $ {{ slp_data.price }}
                            <b class="ml-2" :class="slp_data.change_24 > 0 ? 'text-success' : 'text-danger'">
                                {{ slp_data.change_24 }}%
                                <template v-if="slp_data.change_24 > 0">
                                    <i class="fas fa-level-up-alt text-success"></i>
                                </template>
                                <template v-else>
                                    <i class="fas fa-level-down-alt text-danger"></i>
                                </template>
                            </b>
                            </template>
                      </span>
                    </div>
                    <!-- /.info-box-content -->
                  </div>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
import { bus } from '../../bus.js';

export default {

    data() {
        return {
            data : {},
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

            axie_count : 0,

            loading : false,
            axie_count_loading : false,
            coin_loading : false
        }
    },

    props: {
        slpDataFetchUrl : {
            type : String,
            default : null,
        },
        axieCountFetchUrl : {
            type : String,
            default : null,
        }
    },

    mounted() {
        this.fetchSlpData();
        this.fetchAxieCount();
        this.getCoin('smooth-love-potion');

        setInterval(()=> {
            this.getCoin('smooth-love-potion');
        }, 10000)
    },

    methods : {
        fetchSlpData() {
            this.loading = true;
            axios.get(this.slpDataFetchUrl)
            .then((response)=>{
                this.data = response.data.success ? response.data : this.data;
                bus.$emit('total-slp', this.data.today_slp);
            }).catch(error =>{
                console.log(error);
            }).finally(()=>{
                this.loading = false;
            })
        },

        fetchAxieCount() {
            this.axie_count_loading = true;
            axios.get(this.axieCountFetchUrl)
            .then((response)=>{
                this.axie_count = response.data.success ? response.data.total : 0;
            }).catch(error =>{
                console.log(error);
            }).finally(()=>{
                this.axie_count_loading = false;
            })
        },

        getCoin(coin) {
            this.coin_loading = true;
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
            }).finally(()=>{
                this.coin_loading = false;
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
                    bus.$emit('slp-price', this.slp_data.price);
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
}
</script>


<style type="text/css">
    .claim-btn-warning {
        border-radius: 15px;
        border: 3px solid #f0ad4e;
    }

    .claim-btn-success {
        border-radius: 15px;
        border: 3px solid #5cb85c;
    }

    .claim-btn-secondary {
        border-radius: 15px;
        border: 3px solid #d9e2ef;
    }

    .claim-btn-danger {
        border-radius: 15px;
        border: 3px solid #df4759;
    }
</style>


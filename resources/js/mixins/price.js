export default {
    data() {
        return {
            data : {},
            price_url : 'https://api.coingecko.com/api/v3/coins/',
            slp_data : {
                price : 0,
                change_24 : 0,
            },
            axs_data : {
                price : 0,
                change_24 : 0,
            },
            eth_data : {
                price : 0,
                change_24 : 0,
            },

            loading : false,
            coin_loading : false
        }
    },

    methods : {
        getCoin(coin) {
            this.coin_loading = true;
            let instance = axios.create();
            delete instance.defaults.headers.common['X-CSRF-TOKEN'];
            delete instance.defaults.headers.common['X-Requested-With'];

            const data = instance({
                method: 'get',
                url: this.price_url + coin,
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
        },

        /* you must fetch the crypto price first to use this method */
        covertPrice(value, coin, prefix = '$') {

            let coin_value = parseFloat(value ? value : 0);
            let multiplier = 0;

            switch (coin) {
                case 'smooth-love-potion':
                    multiplier = this.slp_data.price;
                    break;
                case 'ethereum':
                    multiplier = this.eth_data.price;
                    break;
                case 'axie-infinity':
                    multiplier = this.axs_data.price;
                    break;
                default:
                    break;
            }

            return prefix + ' ' + (coin_value * parseFloat(multiplier)).toFixed(2);
        }
    }
}

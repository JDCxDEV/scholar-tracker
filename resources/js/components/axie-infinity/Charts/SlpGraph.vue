<template>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button class="btn btn-secondary mr-2" :disabled="line_loading" :class="getClass(7 , 'line')" @click="initLine(7, false)">Last 7 Days</button>
                        <button class="btn btn-secondary mr-2" :disabled="line_loading" :class="getClass(30 , 'line')" @click="initLine(30, false)">Last 30 Days</button>
                        <button class="btn btn-secondary mr-2" :disabled="line_loading" :class="getClass(0 , 'line')" @click="initLine(0, true)">Last Month</button>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                       <slp-line ref="line" v-if="show_graph" :today_slp="today_slp" @fetching="fetchingLine" :fetchUrl="lineUrl"></slp-line>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button class="btn btn-secondary mr-2" :disabled="pie_loading" :class="getClass(7, 'pie')" @click="initPie(7, false)">Last 7 Days</button>
                        <button class="btn btn-secondary mr-2" :disabled="pie_loading" :class="getClass(30, 'pie')" @click="initPie(30, false)">Last 30 Days</button>
                        <button class="btn btn-secondary mr-2" :disabled="pie_loading" :class="getClass(0, 'pie')" @click="initPie(0, true)">Last Month</button>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <slp-pie ref="pie" v-if="show_graph" @fetching="fetchingPie" :fetch-url="pieUrl"></slp-pie>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</template>

<script>
import SlpLine from "./SlpLine.vue";
import SlpPie from "./SlpPie.vue";
import { bus } from '../../../bus.js';
import methods from '../../../mixins/confirm/methods';

export default {
    components: {
        'slp-line' : SlpLine,
        'slp-pie' : SlpPie,
    },

    mounted() {
        bus.$on('init-graph', (data) => {
            this.init();
        });
        bus.$on('total-slp', (data) => {
            this.today_slp = data;
        });
    },

    props : {
        lineUrl : {
            default : null,
            type : String,
        },
        pieUrl : {
            default : null,
            type : String,
        }
    },

    data() {
        return {
            show_graph : false,
            today_slp : 0,

            line_days : 7,
            pie_days : 7,

            line_loading : false,
            pie_loading : false,
        }
    },

    methods : {
        init() {
            this.show_graph = true;
        },

        getClass(days, type) {
            if(type == 'line') {
                return this.line_days == days ? 'btn-main' : 'btn-secondary';
            }else {
                return this.pie_days == days ? 'btn-main' : 'btn-secondary';
            }
        },

        initLine(days, last_month = false) {
            this.line_days = days;
            this.$refs['line'].init(days, last_month)
        },

        initPie(days, last_month = false) {
            this.pie_days = days;
            this.$refs['pie'].init(days, last_month)
        },

        fetchingLine(value) {
            this.line_loading = value;
        },

        fetchingPie(value) {
            this.pie_loading = value;
        }
    }
};
</script>

<style scoped>
.chart {
    height: 500px;
}
</style>

<template>
<div>
    <template v-if="loading">
        <div class="row chart align-items-center">
            <div class="col-sm-12 text-center">
                <i class="fas fa-circle-notch fa-spin fa-3x"></i>
            </div>
        </div>
    </template>
    <template v-else>
        <v-chart class="chart" :option="options" autoresize />
    </template>
</div>

</template>

<script>
import { use } from "echarts/core";
import { CanvasRenderer } from "echarts/renderers";
import { LineChart } from "echarts/charts";
import { bus } from '../../../bus.js';

import {
  TitleComponent,
  TooltipComponent,
  LegendComponent,
  VisualMapComponent,
  GridComponent,
} from "echarts/components";
import VChart, { THEME_KEY } from "vue-echarts";

use([
  CanvasRenderer,
  TitleComponent,
  TooltipComponent,
  LegendComponent,
  VisualMapComponent,
  GridComponent,
  LineChart
]);

export default {
  components: {
    VChart
  },
  props : {
      fetchUrl : {
          default : null,
          type : String,
      },

      today_slp : {
          default : 0,
          type : Number
      },
  },
  data() {
    return {
        loading : false,
        options : {
            title: {
                text: 'SLP Production',
                subtext: 'Line Graph',
                left: 'center'
            },
            tooltip: {
                trigger: 'axis'
            },
            xAxis: {
                type: 'category',
                data: [],
            },
            yAxis: {
                type: 'value'
            },
            series: [{
                itemStyle: {
                    color: 'rgb(0, 179, 60)'
                },
                data: [],
                type: 'line',
                smooth: true,
            }]
        },
    }
  },

  mounted() {
    this.init();
  },

  methods : {
      init(days = 7, last_month = false) {
            this.$emit('fetching', true);
            this.loading = true;

            axios.post(this.fetchUrl, {
                days : days,
                last_month : last_month
            })
            .then(response =>{
                    let data = response.data;

                    this.options = {
                        title: {
                            text: 'SLP Production',
                            subtext: 'Line Graph',
                            left: 'center'
                        },
                        tooltip: {
                            trigger: 'axis'
                        },
                        xAxis: {
                            type: 'category',
                            data: data.days,
                        },
                        yAxis: {
                            type: 'value'
                        },
                        series: [{
                            itemStyle: {
                                color: 'rgb(0, 179, 60)'
                            },
                            data: data.slp_record,
                            type: 'line',
                            smooth: true,
                        }]
                    }
                this.$emit('fetching', false);
                this.loading = false;
            })
      },
  },

};
</script>

<style scoped>
.chart {
    height: 500px;
}
</style>

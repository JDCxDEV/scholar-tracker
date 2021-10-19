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
        <v-chart class="chart" :option="option" autoresize />
    </template>
</div>

</template>

<script>
import { use } from "echarts/core";
import { CanvasRenderer } from "echarts/renderers";
import { PieChart } from "echarts/charts";

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
  PieChart
]);

export default {
    components: {
        VChart
    },
    props : {
        fetchUrl : {
            default : null,
            type : String,
        }
    },
  data() {
    return {
       option : {},
       loading : false,
    }
  },

    mounted() {
        this.init();
    },

    methods : {
      init(days = 7, last_month = false) {
            this.loading = true;
            this.$emit('fetching', true);

            axios.post(this.fetchUrl, {
                days : days,
                last_month : last_month
            })
            .then(response =>{
                let data = response.data;
                this.option = {
                    title: {
                    text: 'SLP Production',
                    subtext: 'Pie Chart',
                    left: 'center'
                },
                tooltip: {
                    trigger: 'item'
                },
                legend: {
                    orient: 'vertical',
                    left: 'left',
                },
                series: [
                    {
                        name: 'Slp Production',
                        type: 'pie',
                        radius: '50%',
                        data: data.scholars,
                        emphasis: {
                            itemStyle: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                    }
                ]
            };
            this.loading = false;
            this.$emit('fetching', false);
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

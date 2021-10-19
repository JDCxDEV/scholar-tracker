<template>
<div>
    <div class="row mb-5">
        <div class="col-md-4 mb-4">
        <label class="switch">
        <input type="checkbox" :disabled="loading" @change="init(days, type)" v-model="data_type" id="togBtn">
            <div class="slider round">
                <span class="on text-left">SLP</span>
                <span class="off text-right">MMR</span>
            </div>
            </label>
        </div>
        <div class="col-md-8 text-right mb-4">
            <button :disabled="loading" type="button" class="btn mr-2" :class="getClass(7)" @click="init(7, false)">Last 7 Days</button>
            <button :disabled="loading" type="button" class="btn mr-2" :class="getClass(30)"  @click="init(30, false)">Last 30 Days</button>
            <button :disabled="loading" type="button" class="btn mr-2" :class="getClass(0)"  @click="init(0, true)">Last Month</button>
        </div>
    </div>
    <v-chart class="chart" :option="options" autoresize />
</div>
</template>

<script>
import { use } from "echarts/core";
import { CanvasRenderer } from "echarts/renderers";
import { LineChart } from "echarts/charts";

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
        data_type : true,
        loading : false,
        days : 7,
        type : false,
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

      getClass(days) {
            return this.days == days ? 'btn-main' : 'btn-secondary';
      },

      init(days = 7, last_month = false) {
          this.days = days;
          this.type = last_month;

          this.loading = true;

          axios.post(this.fetchUrl, {
              data_type : this.data_type,
              days : this.days,
              last_month : this.type
          }).then(response =>{
                let data = response.data;
                this.options = {
                    title: {
                        text: this.data_type  ? 'SLP Production - Total(' + data.total + ')' + ' Average(' + parseFloat(data.average).toFixed(2) + ')' : 'MMR Rating - Average(' + parseFloat(data.average).toFixed(2) + ')',
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
                        data: data.record,
                        type: 'line',
                        smooth: true,
                    }]
                }
                this.loading = false;
                this.$emit('changeType', this.data_type /* true = slp table & false = battle logs table */);
          })
      },
  },

};
</script>

<style scoped>
.chart {
    height: 500px;
}

.switch {
  position: relative;
  display: inline-block;
  width: 90px;
  height: 34px;
}

.switch input {display:none;}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #545b62;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white ;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #343a40;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(55px);
  -ms-transform: translateX(55px);
  transform: translateX(55px);
}

/*------ ADDED CSS ---------*/
.on
{
  display: none;
}

.on, .off
{
  color: white;
  position: absolute;
  transform: translate(-50%,-50%);
  top: 50%;
  left: 50%;
  font-size: 10px;
}

input:checked+ .slider .on
{display: block;}

input:checked + .slider .off
{display: none;}

/*--------- END --------*/

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>

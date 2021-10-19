<template>
    <div>
        <form-request :submit-url="exportUrl" @load="load" submit-on-success method="POST" :action="exportUrl">
            <filter-box hide-refresh @refresh="fetch">
                <template v-slot:left>
                    <a :hidden="hide" :href="createUrl" class="btn btn-dark mr-2 text-white mt-2 mb-2 btn-block-rounded">
                        <i class="fa fa-plus mr-1"></i>
                        Create Scholar
                    </a>
                    <button  type="button" :hidden="hide" @click="syncBattleLogs"  class="btn btn-dark mr-2 text-white mt-2 mb-2 btn-block-rounded">
                        <i class="fas fa-sync-alt mr-1" :class="syncing_battle_logs ? 'fa-spin' : ''"></i>
                        Sync Battle Logs
                    </button>
                    <!-- <action-button v-if="exportUrl" type="submit" :disabled="loading" class="btn-warning mt-2" icon="fas fa-file-export">Export</action-button> -->
                </template>
                <template v-slot:right>
                    <!-- <selector
                    class="mt-2 mr-2"
                    @change="filter($event, 'email_verified_at')"
                    :items="types"
                    placeholder="Filter by Status">
                    </selector> -->

                    <search-form
                    @search="filter($event, 'search')">
                    </search-form>
                </template>
            </filter-box>
        </form-request>

        <!-- DATATABLE -->
        <data-table
        ref="data-table"
        :headers="headers"
        :filters="filters"
        :fetch-url="fetchUrl"
        :no-action="noAction"
        :disabled="disabled"
        order-desc
        @load="load"
        >

            <template v-slot:body="{ items }">
                <tr v-for="item in items">
                    <td class="align-middle">
                        <button class="btn btn-main btn-sm" @click="clipBoardAddress(item)">
                            <i class="fas fa-clipboard"></i>
                        </button>
                    </td>
                    <td class="align-middle">
                        <p class="m-0">
                            {{ item.last_name +', '+ item.first_name }}
                        </p>
                        <b class="text-secondary">
                            {{ item.name}}
                        </b>
                    </td>
                    <td class="align-middle">
                        <button type="button" class=" btn-block btn text-dark btn-lg claim-btn" :class="getMmrGrade(item.elo, 'class')">
                            <p class="m-0"><b>{{ getMmrGrade(item.elo, 'grade') }}</b></p>
                             <span>{{ item.elo }}</span>
                        </button>
                    </td>
                    <td :hidden="hide" class="align-middle">{{ item.win_rate ? item.win_rate : 0 }}%</td>
                    <td class="align-middle">
                        <p class="mb-1"><img src="https://assets.coingecko.com/coins/images/10366/small/SLP.png" style="width: 20px;" class="image-reponsive">
                        <b>{{ item.average_slp > 0 ? item.average_slp.toFixed(2) : item.today_slp.toFixed(2) }} / day</b></p>
                        <span class="text-muted">(≈${{ parseFloat((item.average_slp > 0 ? item.average_slp : item.today_slp ) * slp_price).toFixed(2) }})</span>
                    </td>
                    <td :hidden="hide" class="align-middle">
                        <p class="mb-1"><img src="https://assets.coingecko.com/coins/images/10366/small/SLP.png" style="width: 20px;" class="image-reponsive">
                        <b>{{ item.today_slp }}</b></p>
                        <span class="text-muted">(≈${{ parseFloat(item.today_slp * slp_price).toFixed(2) }})</span>
                    </td>
                    <td :hidden="hide"  class="align-middle">
                        <p class="mb-1"><img src="https://assets.coingecko.com/coins/images/10366/small/SLP.png" style="width: 20px;" class="image-reponsive">
                        <b>{{ item.claimable_slp }}</b></p>
                        <span class="text-muted">(≈${{ parseFloat(item.claimable_slp * slp_price).toFixed(2) }})</span>
                    </td>
                    <td :hidden="hide" class="align-middle">
                        <p class="mb-1"><img src="https://assets.coingecko.com/coins/images/10366/small/SLP.png" style="width: 20px;" class="image-reponsive">
                        <b>{{ item.total_slp }}</b></p>
                        <span class="text-muted">(≈${{ parseFloat(item.total_slp * slp_price).toFixed(2) }})</span>
                    </td>
                    <td :hidden="hide" class="align-middle">
                        <button type="button" class=" btn-block btn text-dark btn-lg claim-btn" :class="nextClaimClassChecker(item.next_claim)">
                            <p class="mb-0"><b>{{ getDaysLeft(item.next_claim) }}</b></p>
                             <span>{{ item.next_claim }}</span>
                        </button>
                    </td>
                    <td class="align-middle">
                        <view-button color="btn-main" :href="item.showUrl"></view-button>

                        <action-button
                        :hidden="hide"
                        small
                        color="btn-danger"
                        alt-color="btn-warning"
                        :show-alt="item.deleted_at"
                        :action-url="item.archiveUrl"
                        :alt-action-url="item.restoreUrl"
                        icon="fas fa-trash"
                        alt-icon="fas fa-trash-restore-alt"
                        confirm-dialog
                        :disabled="loading"
                        title="Archive Item"
                        alt-title="Restore Item"
                        :message="'Are you sure you want to archive User #' + item.id + '?'"
                        :alt-message="'Are you sure you want to restore User #' + item.id + '?'"
                        @load="load"
                        @success="sync"
                        ></action-button>
                    </td>
                </tr>
            </template>

        </data-table>

        <loader
        :loading="loading">
        </loader>
    </div>
</template>

<script type="text/javascript">
import ListMixin from '../../../mixins/list.js';
import ResponseMixin from '../../../mixins/response.js';

import FormRequest from '../../../components/forms/FormRequest.vue';
import SearchForm from '../../../components/forms/SearchForm.vue';
import Selector from '../../../components/inputs/Select.vue';
import ActionButton from '../../../components/buttons/ActionButton.vue';
import ViewButton from '../../../components/buttons/ViewButton.vue';

import { bus } from '../../../bus.js';

export default {
    data() {
        return {
            types: [
                { value: 1, label: 'Verified' },
                { value: 2, label: 'Unverified' },
            ],

            slp_price : 0,

            syncing_battle_logs : false,
        }
    },

    computed: {
        headers() {
            if(!this.hide){
                return [
                    { text: 'Copy', value: null, icon: null},
                    { text: 'Scholar', value: null, icon: null},
                    { text: 'MMR', value: null, icon: null},
                    { text: 'Winrate', value: null, icon: null},
                    { text: 'Avg Slp', value: null, icon: 'letter'},
                    { text: 'Today Slp', value: null, icon: 'letter'},
                    { text: 'Claimable Slp', value: null, icon: 'number'},
                    { text: 'Total Slp', value: null, icon: 'number'},
                    { text: 'Next Claim', value: null, icon: 'other'},
                ];
            }else {
                return [
                    { text: 'Copy', value: null, icon: null},
                    { text: 'Scholar', value: null, icon: null},
                    { text: 'MMR', value: null, icon: null},
                    { text: 'Avg Slp', value: null, icon: 'letter'},
                ];
            }

        }
    },

    mounted() {
        bus.$on('slp-price', price => {
            this.slp_price = price;
        });
    },

    props: {
        syncBattleLogsUrl : String,
        exportUrl: String,
        createUrl : String,
        hide : Boolean
    },

    methods : {
        clipBoardAddress(item) {
            const el = document.createElement('textarea');
            el.value = item.ronin_address;
            el.setAttribute('readonly', '');
            el.style.position = 'absolute';
            el.style.left = '-9999px';
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);

            this.parseSuccess('Copied scholar ronin address', 'Copied !', {
                 position: "toast-top-left",
            }) 
        },

        syncBattleLogs() {
            this.syncing_battle_logs = true;
            axios.get(this.syncBattleLogsUrl).then(response =>{
                this.syncing_battle_logs = false;
                this.fetch();
            }).catch(error =>{
                console.log(error);
            }).then(() =>{
                this.syncing_battle_logs = false;
            })
        },

        nextClaimClassChecker(date) {
            if(date) {
                return moment().isBefore(moment(date)) ? 'claim-btn-warning' :  'claim-btn-success';
            }
        },

        getDaysLeft(data) {
            var now = moment();
            var expiration = moment(data);
            var minsAverage = expiration.diff(now, "minutes");

            var min = parseInt(minsAverage % 60);
            var hours = parseInt(minsAverage / 60);
            var days = parseInt(hours / 24);
            hours = hours - 24 * days;

            return moment().isAfter(moment(data)) ?  'Now' : 'In ' + days + ' days and ' + hours + ' hours.';
        },

        getMmrGrade(mmr, type) {

            let result = {};

            if(mmr > 1999) {
                result.text = 'High'
                result.class = 'claim-btn-success'
            }

            if(mmr >= 1400 && mmr <= 1999) {
                result.text = 'Above Average'
                result.class = 'claim-btn-warning'
            }

            if(mmr >= 1200 && mmr <= 1399) {
                result.text = 'Average'
                result.class = 'claim-btn-secondary'
            }

            if(mmr <= 1199) {
                result.text = 'Low'
                result.class = 'claim-btn-danger'
            }

            return type == 'grade' ? result.text : result.class;
        }
    },

    mixins: [ ListMixin, ResponseMixin],

    components: {
        'form-request': FormRequest,
        'selector': Selector,
        'search-form': SearchForm,
        'view-button': ViewButton,
        'action-button': ActionButton,
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


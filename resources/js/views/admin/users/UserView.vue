<template>
    <form-request :submit-url="submitUrl" @load="load" @success="init()" confirm-dialog sync-on-success>
        <axie-list ref="game_information" :user="item" :ronin_address="item.ronin_address" :api_url="apiUrl" />
                <div ref="user_modal" class="modal fade" id="user-information-modal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" >Scholar Information</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>First Name</label>
                                        <input v-model="item.first_name" name="first_name" type="text" class="form-control input-sm">
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Last Name</label>
                                        <input v-model="item.last_name" name="last_name" type="text" class="form-control input-sm">
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Email Address</label>
                                        <input v-model="item.email" name="email" type="text" class="form-control input-sm">
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>Scholar Type</label>
                                        <select class="form-control" v-model="item.scholar_type_id" name="scholar_type_id">
                                            <template v-for="type in types">
                                                 <option :key="type.id" :value="type.id">{{ type.name }}</option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-12">
                                        <label>Ronin Address</label>
                                        <input v-model="item.ronin_address" name="ronin_address" type="text" class="form-control input-sm">
                                    </div>
                                </div>
                            </div>
                        <div class="modal-footer">
                            <action-button type="submit" :disabled="loading" class="btn-primary">Save Changes</action-button>
                            <action-button
                            v-if="item.archiveUrl && item.restoreUrl"
                            color="btn-danger"
                            alt-color="btn-warning"
                            :action-url="item.archiveUrl"
                            :alt-action-url="item.restoreUrl"
                            label="Archive"
                            alt-label="Restore"
                            :show-alt="item.deleted_at"
                            confirm-dialog
                            title="Archive Item"
                            alt-title="Restore Item"
                            :message="'Are you sure you want to archive User #' + item.id + '?'"
                            :alt-message="'Are you sure you want to restore User #' + item.id + '?'"
                            :disabled="loading"
                            @load="load"
                            @success="fetch"
                            ></action-button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="qr-modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Account QR Code</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-12  text-center">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f4/QR_code_Ppuripark_Ryoo.jpg/546px-QR_code_Ppuripark_Ryoo.jpg" alt="..." class="img-thumbnail">
                                    <button class="btn btn-info mt-2">
                                        Upload QA Code
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-success mr-auto">
                                Email QR Code
                            </button>

                            <button class="btn btn-secondary">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="scholar-analytics-modal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Scholar Analytics - {{ item.first_name}} {{ item.last_name}}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row my-flex-card">
                                <div class="col-sm-12 col-md-6">
                                    <div class="card card-gray card-outline">
                                        <div class="card-header">
                                            Scholar Daily {{ table_type ? 'SLP' : 'Battle'}} Record
                                        </div>
                                        <div class="card-body">
                                            <template v-if="table_type">
                                                <div class="row mb-4">
                                                    <div class="col-md-6 mt-2">
                                                        <b>Overall farm Slp</b> : <span class="ml-2 badge badge-secondary">{{ item.overall_farm_slp  }}</span>                                               </div>
                                                    <div class="col-md-6 mt-2 text-right">
                                                        <b>Today Slp</b> : <span class="ml-2 badge badge-secondary">
                                                        {{ item.computed_today_slp }}
                                                        ({{ covertPrice(item.computed_today_slp, 'smooth-love-potion', '$') }})</span>
                                                    </div>
                                                </div>
                                                <scholar-daily-record-table :fetchUrl="scholarDailySlpFetchUrl"></scholar-daily-record-table>
                                            </template>
                                            <template v-else>
                                                <battle-logs :ronin-address="item.ronin_address" ></battle-logs>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            <div class="col-sm-12 col-md-6">
                                    <div class="card card-gray card-outline">
                                        <div class="card-header">
                                            Scholar Daily Record Graph
                                        </div>
                                        <div class="card-body">
                                            <slp-line-graph @changeType="getTableType" ref="user-slp-graph-line" :fetch-url="scholarSlpGraphFetchUrl"></slp-line-graph>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </form-request>
</template>

<script type="text/javascript">
import CrudMixin from 'Mixins/crud.js';
import PriceMixin from 'Mixins/price.js';
import NumberMixin from 'Mixins/number.js';

import ActionButton from 'Components/buttons/ActionButton.vue';
import Select from 'Components/inputs/Select.vue';
import ImagePicker from 'Components/inputs/ImagePicker.vue';
import AxieList from 'Components/axie-infinity/AxieList.vue';

import SlpLineGraph from '../scholars/graphs/SlpLineGraph.vue';

import BattleLogs from '../scholars/daily-records/BattleLogs.vue';
import ScholarDailyRecordTable from '../scholars/daily-records/ScholarDailyRecordTable.vue';

export default {

    mounted() {
        this.getCoin('smooth-love-potion');
    },
    methods: {
        fetchSuccess(data) {
            this.item = data.item ? data.item : this.item;
            this.types = data.types ? data.types : this.types;

            if(!this.item.id) {
                $('#user-information-modal').modal('show');
            }else {
                this.$refs.game_information.initData(this.item);
            }
        },

        init() {
            this.fetch();
            $('#user-information-modal').modal('hide');
            this.$refs.game_information.initData(this.item);
        },

        getTableType(type) {
            this.table_type = type;
        }
    },

    data() {
        return {
            item : {},
            types : [],
            user_axies: [],
            table_type : true,  /* true = slp table & false = battle logs table */
        }
    },

    components: {
        'action-button': ActionButton,
        'selector': Select,
        'image-picker': ImagePicker,
        'axie-list' : AxieList,
        'scholar-daily-record-table' : ScholarDailyRecordTable,
        'slp-line-graph' : SlpLineGraph,
        'battle-logs' : BattleLogs,
    },

    props : {
        apiUrl: {
            default: null,
            type: String,
        },

        scholarDailySlpFetchUrl : {
            default: null,
            type: String,
        },

        scholarSlpGraphFetchUrl : {
            default: null,
            type: String,
        }
    },

    mixins: [ CrudMixin, PriceMixin, NumberMixin ],
}
</script>

<style type="text/css">
    .my-flex-card > div > div.card {
        height: calc(100% - 15px);
        margin-bottom: 15px;
    }
</style>

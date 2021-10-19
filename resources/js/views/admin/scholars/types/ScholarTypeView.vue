<template>
    <form-request :submit-url="submitUrl" @load="load" @success="init()" confirm-dialog sync-on-success>
        <card>
            <template v-slot:header>Scholar Type Information</template>

            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <scholar-table hide :fetch-url="scholarsFetchUrl"></scholar-table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-sm-6 col-md-6">
                                            <label>Name</label>
                                            <input v-model="item.name" name="name" type="text" class="form-control input-sm">
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6">
                                            <label>Type</label>
                                            <v-select v-model="item.type" name="type" :disabled="!item.name ? true : false" :items="scholar_types" item-value="value" item-text="name"></v-select>
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6">
                                            <label>SLP Required</label>
                                            <input :readonly="item.type == 'percentage' || !item.type" maxlength="4" v-model="item.slp_required" name="slp_required" type="text" class="form-control input-sm">
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6">
                                            <label>Set SLP Requirement</label>
                                            <v-select :disabled="item.type == 'percentage' || !item.type ? true : false" name="frequency" v-model="item.frequency" :items="frequencies" item-value="value" item-text="name"></v-select>
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6">
                                            <label>Manager Share</label>
                                            <div class="input-group mb-3">
                                            <input v-model="item.manager_share" name="manager_share" type="text" class="form-control input-sm" readonly maxlength="3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><b>%</b></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-6 col-md-6">
                                            <label>Scholar Share</label>
                                            <div class="input-group mb-3">
                                            <input :readonly="item.type == 'quota' || !item.type" v-model="item.scholar_share" name="scholar_share" type="number" class="form-control input-sm" maxlength="3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><b>%</b></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-12 col-md-12">
                                            <label>Description</label>
                                            <div class="input-group mb-3 custom-container">
                                                <textarea class="form-control" v-model="item.description" name="description" rows="9"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

             <template v-slot:footer>
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
             </template>
        </card>
    </form-request>
</template>

<script type="text/javascript">
import CrudMixin from 'Mixins/crud.js';
import ActionButton from 'Components/buttons/ActionButton.vue';
import Selector from 'Components/inputs/VueSelect.vue';
import ScholarTable from '../../users/UserTable.vue';


export default {
    props : {
        scholarsFetchUrl : {
            type : String,
            default : null,
        }
    },

    methods: {
        fetchSuccess(data) {
            this.item = data.item ? data.item : this.item;

            if(!this.item.id) {
                $('#user-information-modal').modal('show');
            }
        },

        init() {
            this.fetch();
        },
    },

    watch : {
        'item.scholar_share'(value) {
            if(value && value <= 100) {
                this.item.manager_share =  this.item.scholar_share ? 100 - this.item.scholar_share : 0;
            }else if(value && value > 100){
                this.item.scholar_share = null;
                this.item.manager_share = null;
            }else {
                this.item.scholar_share = null;
                this.item.manager_share = null;
            }
        },
        'item.slp_required'(value) {
            if(value && value > 0){
                this.item.slp_required = value;
            }
            if(value && value < 10000){
                this.item.slp_required = value;
            }
        },
        'item.type'(value) {
            if(value == 'percentage'){
                this.item.slp_required = null;
                this.item.frequency = null;
            }
            if(value == 'quota'){
                this.item.manager_share = null;
                this.item.scholar_share = null;
            }
        }
    },

    data() {
        return {
            scholar_types: [
                {
                    value : 'percentage',
                    name : 'Percentage',
                },
                {
                    value : 'quota',
                    name : 'Quota',
                },
            ],

            frequencies: [
                {
                    value : 'daily',
                    name : 'Daily',
                },
                {
                    value : 'monthly',
                    name : 'Monthly',
                },
            ],
        }
    },

    components: {
        'action-button': ActionButton,
        'v-select': Selector,
        'scholar-table' : ScholarTable,
    },

    mixins: [ CrudMixin ],
}
</script>

<style>

.custom-container > textarea
{
    width: 100%;
    height: 100%;
    box-sizing: border-box;
}

.custom-container > div
{
    width: 100%;
    height: 100%;
}
</style>

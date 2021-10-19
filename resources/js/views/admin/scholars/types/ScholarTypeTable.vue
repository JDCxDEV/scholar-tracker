<template>
    <div>
        <form-request :submit-url="exportUrl" @load="load" submit-on-success method="POST" :action="exportUrl">
            <filter-box hide-refresh @refresh="fetch">
                <template v-slot:left>
                    <a :href="createUrl" class="btn btn-dark mr-2 text-white mt-2 mb-2 btn-block-rounded">
                        <i class="fa fa-plus mr-1"></i>
                        Create Scholar Type
                    </a>
                </template>
                <template v-slot:right>
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
                    <td class="align-middle">{{ item.name }}</td>
                    <td class="align-middle">{{ item.type }}</td>
                    <td class="align-middle">{{ item.scholars }}</td>
                    <td class="align-middle text-center">
                        <view-button  class="btn btn-sm btn-main" :href="item.showUrl"></view-button>

                        <action-button
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
import ListMixin from 'Mixins/list.js';
import FormRequest from 'Components/forms/FormRequest.vue';
import SearchForm from 'Components/forms/SearchForm.vue';
import Selector from 'Components/inputs/Select.vue';
import ActionButton from 'Components/buttons/ActionButton.vue';
import ViewButton from 'Components/buttons/ViewButton.vue';

export default {
    data() {
        return {
            types: [
                { value: 1, label: 'Verified' },
                { value: 2, label: 'Unverified' },
            ]
        }
    },

    computed: {
        headers() {
            return [
                { text: 'Name', value: 'name', icon: 'letter'},
                { text: 'Type', value: 'type', icon: 'letter'},
                { text: 'Total Scholars', value: null, icon: 'number'},
            ];
        }
    },

    props: {
        exportUrl: String,
        createUrl : String,
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
        }
    },

    mixins: [ ListMixin ],

    components: {
        'form-request': FormRequest,
        'selector': Selector,
        'search-form': SearchForm,
        'view-button': ViewButton,
        'action-button': ActionButton,
    },
}
</script>

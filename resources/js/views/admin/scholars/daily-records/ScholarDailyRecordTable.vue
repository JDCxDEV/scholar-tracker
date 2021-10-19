<template>
    <div>
        <!-- DATATABLE -->
        <data-table
        customStyle="height: 500px;"
        ref="data-table"
        :headers="headers"
        :filters="filters"
        :fetch-url="fetchUrl"
        :no-action="true"
        :disabled="disabled"
        order-desc
        @load="load"
        >

            <template v-slot:body="{ items }">
                <tr v-for="item in items">
                    <td>{{ item.date }}</td>
                    <td>{{ item.slp }}</td>
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

export default {
    data() {
        return {

        }
    },

    computed: {
        headers() {
            return [
                { text: 'Date', value: 'date', icon: 'others'},
                { text: 'Slp', value: 'slp', icon: 'number'},
            ];
        }
    },

    props: {
        fetchUrl: String,
    },

    mixins: [ ListMixin ],

    components: {

    },
}
</script>

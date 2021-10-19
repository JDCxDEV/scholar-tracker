<template>
    <div class="form-group">
        <input type="text" v-model="selected" :name="name" hidden >
        <div class="input-group">
            <v-select class="w-100" ref="select" :disabled="disabled" v-model="selected" :reduce="item => item[itemValue]" :label="itemText" :options="items"></v-select>
        </div>
    </div>
</template>


<script type="text/javascript">
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

export default {
    mounted() {
        this.selected = this.value;
    },

    methods: {
        change() {
            this.$emit('change', this.selected);
        },
    },

    watch: {
        value(value) {
            this.selected = value;
        },

        selected(value) {
            this.change();
            if(!value) {
                this.$refs.select.clearSelection();
            }
        },
    },

    data() {
        return {
            selected: {},
        }
    },

    props: {
        items: {},

        value: {},

        name: {
            default: null,
            type: String,
        },

        itemText: {
            default: 'label',
            type: String,
        },

        itemValue: {
            default: 'value',
            type: String,
        },

        label: String,
        labelNote: String,
        labelNoteClass: {
            default: 'text-danger',
            type: String,
        },

        placeholder: String,
        emptyText: String,

        disabled: {
            default: false,
            type: Boolean,
        },
    },

    model: {
        prop: 'value',
        event: 'change',
    },

    components: {
        'v-select': vSelect ,
    }
}
</script>

<style type="text/scss">


</style>

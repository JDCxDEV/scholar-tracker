<template>
    <div class="form-group">
        <label>{{ label }} <small v-if="labelNote" :class="labelNoteClass">{{ labelNote }}</small></label>

        <div class="input-group">
            <div class="input-group-prepend">
                <button @click="clear" type="button" class="btn btn-main clear-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <selectize v-model="selected" @change="change" :multiple="multiple" :name="name" :settings="settings">
                <option v-for="item in items" :value="item[itemValue]">{{ item[itemText] }}</option>
            </selectize>
        </div>

    </div>
</template>


<script type="text/javascript">
import Selectize from 'vue2-selectize';
import selectizecss from 'selectize/dist/css/selectize.css';

export default {
    mounted() {
        this.selected = this.value;
    },

    methods: {
        change() {
            this.$emit('change', this.selected);
        },

        clear() {
            this.selected = this.multiple ? [] : null;
        },
    },

    computed: {
        settings() {
            return {
                plugins: ['remove_button'],
                placeholder: this.placeholder,
            }
        },
    },

    watch: {
        value(value) {
            this.selected = value;
        },

        selected(value) {
            this.change();
        },
    },

    data() {
        return {
            selected: [],
        }
    },

    props: {
        items: {},

        value: {},

        name: {
            default: null,
            type: String,
        },

        multiple: {
            default: false,
            type: Boolean,
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
        'selectize': Selectize,
    }
}
</script>

<style type="text/css">
.selectize-control {
    min-width: 150px;
}

.clear-btn {
    height: 2.5em;
}

.selectize-input {
    padding: 4.5px 7px;
}

.selectize-control.multi .selectize-input.has-items {
    padding: 4.5px;
}

.selectize-control.multi .selectize-input > div {
    margin-bottom: 0px;
}
</style>

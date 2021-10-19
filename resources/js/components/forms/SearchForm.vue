<template>
    <div class="form-group">
        <form @submit.prevent="search" method="GET" action="#">
            <div class="input-group" :class="small ? 'input-group-sm' : ''">
                <div class="input-group-prepend">
                    <button @click="clear" type="button" class="btn btn-main">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <input v-model="value" type="text" :name="name" placeholder="Search here..." aria-label="Search here..." class="form-control">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-main">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>

<script type="text/javascript">
export default {
    methods: {
        search() {
            this.$emit('search', this.value);
        },
        clear() {
            this.value = null;
            this.$emit('search', this.value);
        }
    },

    data() {
        return {
            value: null,
        }
    },

    props: {
        small: {
            default: false,
            type: Boolean,
        },

        name: String,
    },
}
</script>

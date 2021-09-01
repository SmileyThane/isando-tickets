<template>
    <v-select
        :items="items"
        v-model="selectedItems"
        item-value="id"
        item-text="name"
        placeholder="Choose client"
        class="mx-2"
        multiple
        clearable
        outlined
    ></v-select>
</template>

<script>
export default {
    name: 'client-select',
    props: {
        items: {
            type: Array,
            required: true,
        },
        selected: {
            type: Array,
            required: true,
        }
    },
    data () {
        return {

        };
    },
    created () {

    },
    methods: {

    },
    computed: {
        selectedItems: {
            get() {
                return this.selected;
            },
            set(values) {
                this.$emit('select', values);
            }
        }
    }
};
</script>

<style scoped>
>>>.v-input__slot {
    min-height: 50px !important;
}
</style>

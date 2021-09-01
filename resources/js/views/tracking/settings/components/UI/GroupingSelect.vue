<template>
    <v-select
        :items="items"
        v-model="selectedItems"
        item-value="id"
        item-text="name"
        placeholder="Group by ..."
        class="mx-2"
        outlined
    ></v-select>
</template>

<script>
export default {
    name: 'grouping-select',
    props: {
        selected: {
            type: String,
            required: true,
        }
    },
    data () {
        return {
            items: [
                {
                    name: 'Client',
                    id: 'client'
                },
                {
                    name: 'Project',
                    id: 'project'
                },
                {
                    name: 'Coworker',
                    id: 'coworker'
                }
            ],
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

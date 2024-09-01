<template>
    <div>
        <v-tabs v-model="customerActiveTab" :color="themeBgColor" align-with-title>
            <v-tab
                v-for="(item, index) in items"
                @click="handleChangeCustomerTab(index)"
                :key="item"
            >
                {{ item }}
            </v-tab>
        </v-tabs>

        <v-tabs-items v-model="customerActiveTab">
            <v-tab-item v-for="item in items" :key="item">
                <v-card flat>
                    <basic-view
                        @onSetCompanyName="onSetCompanyName"
                        v-if="item === 'basic'"
                    />
                    <tickets
                        :searchValue="companyName"
                        searchLabel="Company"
                        pageName="customer"
                        :searchDisabled="true"
                        v-else
                    />
                </v-card>
            </v-tab-item>
        </v-tabs-items>
    </div>
</template>

<script>
import BasicView from './basic.vue';
import TicketsView from '../../components/tickets/tickets.vue';

export default {
    props: {
        tab: 0,
    },
    components: {
        'basic-view': BasicView,
        tickets: TicketsView,
    },
    data() {
        return {
            themeBgColor: this.$store.state.themeBgColor,
            customerActiveTab: this.$route.query.customerActiveTab ? parseInt(this.$route.query.customerActiveTab) : 0,
            items: ['basic', 'tickets'],
            companyName: '',
        };
    },
    methods: {
        onSetCompanyName(name) {
            this.companyName = name;
        },
        handleChangeCustomerTab(key) {
            if (key !== this.$route.query.customerActiveTab) {
                this.$router.push({
                    name: 'customer',
                    query: {
                        ...this.$route.query,
                        customerActiveTab: key,
                    },
                });
            }
        },
    },
};
</script>
<script setup></script>

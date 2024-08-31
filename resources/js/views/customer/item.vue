<template>
  <div>
    <v-tabs v-model="employeeActiveTab" :color="themeBgColor" align-with-title>
      <v-tab
        v-for="(item, index) in items"
        @click="handleChangeCustomerTab(index)"
        :key="item"
      >
        {{ item }}
      </v-tab>
    </v-tabs>

    <v-tabs-items v-model="employeeActiveTab">
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
  mounted() {
    this.$router.push({
      name: 'customer',
      query: this.$route.query,
    });
  },
  data() {
    return {
      themeBgColor: this.$store.state.themeBgColor,
      employeeActiveTab: this.tab,
      items: ['basic', 'tickets'],
      companyName: '',
    };
  },
  watch: {
    employeeActiveTab(value) {
      console.log(value);
      this.$router.push({
        name: 'customer',
        query: { ...this.$route.query, customerTab: value, page: 1, tab: 1 },
      });
    },
    '$route.query.customerTab': function (value) {
      if (+this.employeeActiveTab !== +value) {
        this.employeeActiveTab = +value;
      }
    },
  },
  methods: {
    onSetCompanyName(name) {
      this.companyName = name;
    },
    handleChangeCustomerTab(key) {
      this.employeeActiveTab = key;
      this.$router.push({
        name: 'customer',
        query: {
          ...this.$route.query,
          customerTab: key,
        },
      });
    },
  },
};
</script>
<script setup></script>

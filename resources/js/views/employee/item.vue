<template>
  <v-container fluid>
    <v-tabs v-model="employeeActiveTab" :color="themeBgColor" style="height: 40px;">
      <v-tab
        v-for="(item, index) in items"
        @click="handleChangeCustomerTab(index)"
        :key="item"
        style="height: 40px;"
      >
        {{ item }}
      </v-tab>
    </v-tabs>

    <v-tabs-items v-model="employeeActiveTab">
      <v-tab-item v-for="item in items" :key="item">
        <v-card flat>
          <basic-employee
            @onSetContactName="onSetContactName"
            v-if="item === 'basic'"
          />

          <tickets
            :searchValue="contactName"
            searchLabel="Contact"
            pageName="individual"
            :searchDisabled="true"
            v-if="item === 'tickets'"
          />

          <EmployeeAdditionalTab
            v-if="item === 'additional'"
            @onSetContactName="onSetContactName"
          />
        </v-card>
      </v-tab-item>
    </v-tabs-items>
  </v-container>
</template>

<style scoped>
  .v-tabs /deep/ .v-tabs-bar {
      height: 40px!important;
  }
</style>

<script>
import BasicEmployee from './basic';
import tickets from '../../components/tickets/tickets';
import employeeAdditionalTab from '../../components/employee/EmployeeAdditionalTab.vue';

export default {
  components: {
    tickets: tickets,
    'basic-employee': BasicEmployee,
    EmployeeAdditionalTab: employeeAdditionalTab,
  },
  data() {
    return {
      items: ['basic', 'tickets', 'additional'],
      contactName: '',
      employeeActiveTab: this.$route.query.employeeActiveTab
        ? parseInt(this.$route.query.employeeActiveTab)
        : 0,
      themeBgColor: this.$store.state.themeBgColor,
    };
  },
    mounted() {
        this.getUser(this.$route.params.id)
    },
  methods: {
      getUser(){
          axios.get(`/api/user/find/${this.$route.params.id}`).then(response => {
              response = response.data
              if (response.success === true) {
                  this.onSetContactName(response.data.email);
              }
          });
      },
    onSetContactName(name) {
      this.contactName = name;
    },
    handleChangeCustomerTab(key) {
      if (key !== this.$route.query.employeeActiveTab) {
        this.$router.push({
          name: 'individual',
          query: {
            ...this.$route.query,
            employeeActiveTab: key,
          },
        });
      }
    },
  },
  $route() {
    if (this.$route.query.employeeActiveTab) {
      this.employeeActiveTab = parseInt(this.$route.query.employeeActiveTab);
    }

    if (typeof this.$route.query.employeeActiveTab === 'undefined') {
      this.employeeActiveTab = 0;
    }
  },
};
</script>

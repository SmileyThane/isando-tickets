<template>
  <v-container fluid>
    <v-toolbar flat>
      <v-tabs v-model="activeTab" :color="themeBgColor">
        <v-tab v-for="item in tabs" :key="item.id" :value="item.id"
          >{{ item.name }}
        </v-tab>
      </v-tabs>
      <v-spacer></v-spacer>
      <v-tooltip left>
        <template v-slot:activator="{ on, attrs }">
          <v-btn text v-bind="attrs" v-on="on">
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
        </template>
        <span>Coming soon</span>
      </v-tooltip>
    </v-toolbar>

    <ticketsTable
      :active-tab="activeTab"
      :searchValue="contactName"
      searchLabel="Contact"
      :searchDisabled="true"
      :tabs="tabs"
      pageName="individual"
      :tab="tab"
      :page="page"
    />
  </v-container>
</template>

<style scoped>
.v-data-table /deep/ .sticky-header {
  position: sticky;
}

.v-data-table /deep/ .v-data-table__wrapper {
  overflow: unset;
  z-index: 5;
  position: relative;
  background: #fff;
}
</style>

<script>
import ticketsTable from '../../components/tickets/ticketsTable';

export default {
  name: 'tickets',
  components: { ticketsTable },
  props: {
    page: {
      type: [String, Number],
      default: 1,
    },
    tab: {
      type: [String, Number],
      default: 1,
    },
    contactName: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      langMap: this.$store.state.lang.lang_map,
      themeFgColor: this.$store.state.themeFgColor,
      themeBgColor: this.$store.state.themeBgColor,
      snackbar: false,
      actionColor: '',
      snackbarMessage: '',
      searchLabel: 'Company',
      tabs: [
        { id: 0, name: this.$store.state.lang.lang_map.ticket_tabs.all },
        { id: 1, name: this.$store.state.lang.lang_map.ticket_tabs.open },
        { id: 2, name: this.$store.state.lang.lang_map.ticket_tabs.my_tickets },
        {
          id: 3,
          name: this.$store.state.lang.lang_map.ticket_tabs.urgent_open,
        },
        {
          id: 4,
          name: this.$store.state.lang.lang_map.ticket_tabs.internal_open,
        },
        { id: 5, name: this.$store.state.lang.lang_map.ticket_tabs.projects },
        { id: 6, name: this.$store.state.lang.lang_map.ticket_tabs.spam },
        { id: 7, name: this.$store.state.lang.lang_map.ticket_tabs.closed },
      ],
      activeTab: this.tab ? parseInt(this.tab) : 1,
    };
  },
  watch: {
    activeTab(value) {
      this.$router.push({
        name: 'individual',
        query: { ...this.$route.query, tab: value, page: 1 },
      });
    },
    '$route.query.tab': function (value) {
      if (+this.activeTab !== +value) {
        this.activeTab = +value;
      }
    },
  },
};
</script>

<style>
.v-data-table-header th {
  white-space: nowrap;
}

.v-data-table > .v-data-table__wrapper > table > tbody > tr > td,
.v-data-table > .v-data-table__wrapper > table > tfoot > tr > td,
.v-data-table > .v-data-table__wrapper > table > thead > tr > td {
  height: 0;
}
</style>

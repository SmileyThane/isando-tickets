<template>
  <div>
    <v-snackbar
      v-model="snackbar"
      :bottom="true"
      :color="actionColor"
      :right="true"
    >
      {{ snackbarMessage }}
    </v-snackbar>
    <v-container>
      <v-row>
        <v-col
          v-for="(item, index) in items"
          :key="index"
          cols="12"
          sm="6"
          md="4"
          class="d-flex"
        >
          <v-card class="flex-grow-1 card-equal-height justify-content-between">
            <v-card-title class="d-flex justify-space-between align-center">
              <span>{{ item.labelPath }}</span>
              <v-checkbox
                v-model="item.isHidden"
                :input-value="item.isHidden"
                class="checkbox-right mt-0"
              ></v-checkbox>
            </v-card-title>
            <v-card-text class="px-0">
              <div v-if="item.children.length">
                <div
                  v-for="(child, childIndex) in item.children"
                  :key="childIndex"
                  class="d-flex justify-space-between align-center px-4"
                >
                  <span class="black--text">{{ child.labelPath }}</span>
                  <v-checkbox
                    v-model="child.isHidden"
                    :input-value="child.isHidden"
                    class="checkbox-right mt-0"
                  ></v-checkbox>
                </div>
              </div>
              <div class="px-4 grey--text" v-else>No items</div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<style>
.checkbox-col {
  display: flex;
  align-items: center;
  height: 32px;
}

.v-input--selection-controls__input {
  height: 24px;
}

.v-messages {
  display: none;
}
</style>

<script>
export default {
  name: 'sidebar',
  data() {
    return {
      snackbar: false,
      actionColor: '',
      snackbarMessage: '',
      items: [
        {
          key: 'home',
          labelPath: this.$store.state.lang.lang_map.sidebar.home,
          isHidden: true,
          children: [],
        },
        {
          key: 'knowledge_base',
          labelPath: this.$store.state.lang.lang_map.sidebar.knowledge_base,
          isHidden: true,
          children: [],
        },
        {
          key: 'risk_repository',
          labelPath: this.$store.state.lang.lang_map.sidebar.risk_repository,
          isHidden: true,
          children: [],
        },
        {
          key: 'incident_reporting',
          labelPath: this.$store.state.lang.lang_map.sidebar.incident_reporting,
          isHidden: true,
          children: [
            {
              key: 'create_incident',
              labelPath:
                this.$store.state.lang.lang_map.sidebar.create_incident,
              isHidden: true,
            },
            {
              key: 'incident_reporting_list',
              labelPath:
                this.$store.state.lang.lang_map.sidebar.incident_reporting,
              isHidden: true,
            },
            {
              key: 'scenarios',
              labelPath:
                this.$store.state.lang.lang_map.sidebar
                  .incident_reporting_scenarios,
              isHidden: true,
            },
            {
              key: 'action_boards',
              labelPath:
                this.$store.state.lang.lang_map.sidebar
                  .incident_reporting_action_boards,
              isHidden: true,
            },
          ],
        },
        {
          key: 'licenses',
          labelPath: this.$store.state.lang.lang_map.sidebar.custom_licenses,
          isHidden: true,
          children: [],
        },
        {
          key: 'unmapped',
          labelPath:
            this.$store.state.lang.lang_map.sidebar.custom_license_unassigned,
          isHidden: true,
          children: [],
        },
        {
          key: 'crm',
          labelPath: this.$store.state.lang.lang_map.sidebar.crm,
          isHidden: true,
          children: [
            {
              key: 'all',
              labelPath: this.$store.state.lang.lang_map.sidebar.all,
              isHidden: true,
            },
            {
              key: 'companies',
              labelPath: this.$store.state.lang.lang_map.sidebar.companies,
              isHidden: true,
            },
            {
              key: 'individuals',
              labelPath: this.$store.state.lang.lang_map.sidebar.individuals,
              isHidden: true,
            },
          ],
        },
        {
          key: 'products',
          labelPath: this.$store.state.lang.lang_map.sidebar.products,
          isHidden: true,
          children: [],
        },
        {
          key: 'support',
          labelPath: this.$store.state.lang.lang_map.sidebar.ticket_list_menu,
          isHidden: true,
          children: [
            {
              key: 'ticket_list',
              labelPath: this.$store.state.lang.lang_map.sidebar.ticket_list,
              isHidden: true,
            },
            {
              key: 'create_ticket',
              labelPath: this.$store.state.lang.lang_map.sidebar.create_ticket,
              isHidden: true,
            },
          ],
        },
        {
          key: 'time_tracking',
          labelPath: this.$store.state.lang.lang_map.sidebar.time_tracking,
          isHidden: true,
          children: [
            {
              key: 'tracking_dashboard',
              labelPath:
                this.$store.state.lang.lang_map.sidebar.tracking_dashboard,
              isHidden: true,
            },
            {
              key: 'tracking_tracker',
              labelPath:
                this.$store.state.lang.lang_map.sidebar.tracking_tracker,
              isHidden: true,
            },
            {
              key: 'tracking_calendar',
              labelPath:
                this.$store.state.lang.lang_map.sidebar.tracking_calendar,
              isHidden: true,
            },
            {
              key: 'tracking_projects',
              labelPath:
                this.$store.state.lang.lang_map.sidebar.tracking_projects,
              isHidden: true,
            },
            {
              key: 'tracking_reports',
              labelPath:
                this.$store.state.lang.lang_map.sidebar.tracking_reports,
              isHidden: true,
            },
            {
              key: 'tracking_settings',
              labelPath:
                this.$store.state.lang.lang_map.sidebar.tracking_settings,
              isHidden: true,
            },
          ],
        },
        {
          key: 'notifications',
          labelPath: this.$store.state.lang.lang_map.sidebar.notifications,
          isHidden: true,
          children: [
            {
              key: 'notify_customers',
              labelPath:
                this.$store.state.lang.lang_map.sidebar.notify_customers,
              isHidden: true,
            },
            {
              key: 'notifications_history',
              labelPath:
                this.$store.state.lang.lang_map.sidebar.notifications_history,
              isHidden: true,
            },
          ],
        },
        {
          key: 'settings',
          labelPath: this.$store.state.lang.lang_map.sidebar.settings,
          isHidden: true,
          children: [
            {
              key: 'companies',
              labelPath: this.$store.state.lang.lang_map.sidebar.companies,
              isHidden: true,
            },
            {
              key: 'teams',
              labelPath: this.$store.state.lang.lang_map.sidebar.teams,
              isHidden: true,
            },
            {
              key: 'system_settings',
              labelPath:
                this.$store.state.lang.lang_map.sidebar.system_settings,
              isHidden: true,
            },
            {
              key: 'incident_reporting',
              labelPath:
                this.$store.state.lang.lang_map.sidebar.incident_reporting,
              isHidden: true,
            },
            {
              key: 'roles',
              labelPath: this.$store.state.lang.lang_map.sidebar.roles,
              isHidden: true,
            },
            {
              key: 'sidebar_access',
              labelPath: this.$store.state.lang.lang_map.sidebar.sidebar_access,
              isHidden: true,
            },
          ],
        },
      ],
    };
  },
};
</script>

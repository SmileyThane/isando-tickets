<template>
  <v-card v-if="$helpers.auth.checkPermissionByIds([88])">
    <v-toolbar :color="themeBgColor" dark dense flat>
      <v-toolbar-title :style="`color: ${themeFgColor};`"
        >{{ langMap.profile.internal_billing }}
      </v-toolbar-title>
      <v-spacer></v-spacer>
    </v-toolbar>
    <v-card-text>
      <v-row>
        <v-col cols="12">
          <v-list-item v-for="item in userData.billing" :key="item.id">
            <v-list-item-content>
              <v-list-item-title v-text="item.name"></v-list-item-title>
              <v-list-item-subtitle
                v-text="item.cost + ' ' + currency.symbol"
              ></v-list-item-subtitle>
            </v-list-item-content>
            <v-list-item-action>
              <v-icon small @click="editInternalBilling(item)">
                mdi-pencil
              </v-icon>
            </v-list-item-action>
            <v-list-item-action>
              <v-icon small @click="deleteInternalBilling(item.id)">
                mdi-delete
              </v-icon>
            </v-list-item-action>
          </v-list-item>
        </v-col>
        <v-col cols="12">
          <v-expansion-panels
            v-model="internalBillingEditor"
            accordion
            multiple
          >
            <v-expansion-panel>
              <v-expansion-panel-header>
                {{ langMap.main.add }}
                <template v-slot:actions>
                  <v-icon
                    :color="themeBgColor"
                    :style="`color: ${themeFgColor};`"
                  >
                    mdi-plus
                  </v-icon>
                </template>
              </v-expansion-panel-header>
              <v-expansion-panel-content>
                <v-form>
                  <v-row>
                    <v-col cols="8">
                      <v-text-field
                        v-model="internalBillingForm.name"
                        :color="themeBgColor"
                        :item-color="themeBgColor"
                        :label="langMap.main.name"
                        dense
                      />
                    </v-col>
                    <v-col cols="3">
                      <v-text-field
                        v-model="internalBillingForm.cost"
                        :color="themeBgColor"
                        :item-color="themeBgColor"
                        :label="langMap.main.cost"
                        dense
                      />
                    </v-col>
                    <v-col v-if="currency" cols="1">
                      <v-text-field
                        v-model="currency.symbol"
                        :color="themeBgColor"
                        :item-color="themeBgColor"
                        :label="langMap.tracking.settings.currency"
                        dense
                        readonly
                      />
                    </v-col>
                    <v-btn
                      v-if="!internalBillingForm.id"
                      :color="themeBgColor"
                      bottom
                      dark
                      fab
                      right
                      small
                      @click="createInternalBilling"
                    >
                      <v-icon
                        :color="themeBgColor"
                        :style="`color: ${themeFgColor};`"
                        >mdi-plus
                      </v-icon>
                    </v-btn>
                    <v-btn
                      v-if="internalBillingForm.id"
                      :color="themeBgColor"
                      bottom
                      dark
                      fab
                      right
                      small
                      @click="updateInternalBilling(internalBillingForm.id)"
                    >
                      <v-icon
                        :color="themeBgColor"
                        :style="`color: ${themeFgColor};`"
                        >mdi-update
                      </v-icon>
                    </v-btn>
                  </v-row>
                </v-form>
              </v-expansion-panel-content>
            </v-expansion-panel>
          </v-expansion-panels>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
import moment from 'moment-timezone';
import EventBus from '../../EventBus.vue';

export default {
  name: 'InternalBillingCard',
  props: {
    onSetContactName: {
      type: Function,
      default: null,
    },
  },
  data() {
    return {
      themeFgColor: this.$store.state.themeFgColor,
      themeBgColor: this.$store.state.themeBgColor,
      langMap: this.$store.state.lang.lang_map,
      snackbar: false,
      actionColor: '',
      snackbarMessage: '',
      userData: {
        id: '',
        title: '',
        title_before_name: '',
        surname: '',
        country: '',
        anredeform: '',
        lang: '',
        name: '',
        email: '',
        phones: [],
        addresses: [],
        emails: [],
        socials: [],
        status: '',
        notification_statuses: [],
        number: '',
        avatar_url: '',
        deleted_at: null,
        filter_groups: [],
        employee: {
          notes: '',
        },
        billing: null,
      },
      currency: {
        symbol: '',
      },
      internalBillingEditor: null,
      internalBillingForm: {},
      primaryEmailId: null,
      companies: [],
      employeeForm: {
        client_id: null,
        company_user_id: null,
        description: '',
      },
      activityForm: {
        model_id: null,
        model_type: 'App\\CompanyUser',
        date: moment().format('YYYY-MM-DD'),
        time: moment().format('H:m'),
        files: null,
      },
      notificationStatuses: [],
      avatar: '',
    };
  },
  mounted() {
    this.getUser();
    let that = this;
    EventBus.$on('update-theme-fg-color', function (color) {
      that.themeFgColor = color;
    });
    EventBus.$on('update-theme-bg-color', function (color) {
      that.themeBgColor = color;
    });
  },
  methods: {
    getUser() {
      axios.get(`/api/user/find/${this.$route.params.id}`).then((response) => {
        response = response.data;
        if (response.success === true) {
          this.$emit('onSetContactName', response.data.email);
          this.userData = response.data;
          this.primaryEmailId = this.userData.email_id;
          const employeeCompanies =
            this.userData.employee.assigned_to_clients.filter(
              (item) => item.deleted_at === null
            );
          this.companies =
            employeeCompanies.length > 0
              ? employeeCompanies
              : this.userData.companies;
          this.employeeForm.company_user_id = this.userData.employee.id;
          this.activityForm.model_id = this.userData.employee.id;
          if (this.userData.notification_statuses.length) {
            let that = this;
            this.userData.notification_statuses.forEach(function (item) {
              if (item.status !== 0) {
                that.notificationStatuses.push(item.status);
              }
            });
          } else {
            this.notificationStatuses = [101, 102, 103, 201, 202, 301, 302];
          }

          if (this.userData.avatar_url) {
            this.avatar = this.userData.avatar_url;
          } else {
            this.avatar =
              'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8Xw8AAoMBgDTD2qgAAAAASUVORK5CYII=';
          }
          this.userData.filter_groups = this.userData.client_filter_groups.map(
            (group) => {
              return group.data.name;
            }
          );
        } else {
          this.snackbarMessage = this.langMap.main.generic_error;
          this.actionColor = 'error';
          this.snackbar = true;
        }
      });
    },
    editInternalBilling(item) {
      if (this.internalBillingEditor === null) {
        this.internalBillingEditor = [0];
        this.internalBillingForm.id = item.id;
        this.internalBillingForm.name = item.name;
        this.internalBillingForm.cost = item.cost;
      } else {
        this.internalBillingEditor = null;
        this.internalBillingForm = {};
      }
    },
    deleteInternalBilling(id) {
      axios.delete(`/api/billing/internal/${id}`).then((response) => {
        response = response.data;
        if (response.success === true) {
          this.snackbarMessage = this.langMap.main.update_successful;
          this.actionColor = 'success';
          this.snackbar = true;
          this.getUser();
        } else {
          this.snackbarMessage = this.langMap.main.generic_error;
          this.actionColor = 'error';
          this.snackbar = true;
        }
      });
    },
    createInternalBilling() {
      this.internalBillingForm.entity_id = this.userData.id;
      this.internalBillingForm.entity_type = 'App\\User';
      axios
        .post(`/api/billing/internal`, this.internalBillingForm)
        .then((response) => {
          response = response.data;
          if (response.success === true) {
            this.snackbarMessage = this.langMap.main.update_successful;
            this.actionColor = 'success';
            this.snackbar = true;
            this.internalBillingEditor = null;
            this.internalBillingForm = {};
            this.getUser();
          } else {
            this.snackbarMessage = this.langMap.main.generic_error;
            this.actionColor = 'error';
            this.snackbar = true;
          }
        });
    },
    updateInternalBilling(id) {
      axios
        .put(`/api/billing/internal/${id}`, this.internalBillingForm)
        .then((response) => {
          response = response.data;
          if (response.success === true) {
            this.snackbarMessage = this.langMap.main.update_successful;
            this.actionColor = 'success';
            this.snackbar = true;
            this.internalBillingEditor = null;
            this.internalBillingForm = {};
            this.getUser();
          } else {
            this.snackbarMessage = this.langMap.main.generic_error;
            this.actionColor = 'error';
            this.snackbar = true;
          }
        });
    },
  },
};
</script>

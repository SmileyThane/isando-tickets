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
          <v-list-item v-for="item in client.billing" :key="item.id">
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
  name: 'CustomerInternalBillingCard',
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
      client: {
        client_name: '',
        short_name: '',
        client_description: '',
        supplier_object: {},
        products: [
          {
            product_data: {},
          },
        ],
        phones: [],
        addresses: [],
        socials: [],
        employees: [
          {
            employee: {
              user_id: '',
              company_id: '',
              roles: [],
              user_data: {
                contact_phone: {},
                phones: [],
                addresses: [],
              },
            },
          },
        ],
        logo_url: '',
        is_active: false,
        notes: '',
        filter_groups: [],
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
      supplierEmployees: [],
      isCompanyContactsLoading: false,
      loadingEmployees: this.themeBgColor,
      loadingActivities: this.themeBgColor,
    };
  },
  mounted() {
    this.getClient();
    let that = this;
    EventBus.$on('update-theme-fg-color', function (color) {
      that.themeFgColor = color;
    });
    EventBus.$on('update-theme-bg-color', function (color) {
      that.themeBgColor = color;
    });
  },
  methods: {
    getClient() {
      this.isCompanyContactsLoading = true;
      this.loadingEmployees = this.themeBgColor;
      this.loadingActivities = this.themeBgColor;
      axios.get(`/api/client/${this.$route.params.id}`).then((response) => {
        response = response.data;
        if (response.success === true) {
          this.$emit('onSetCompanyName', response.data.supplier_name);
          this.client = response.data;
          this.client.client_name = response.data.name;
          this.client.client_description = response.data.description;
          this.client.supplier_object = {};
          this.client.supplier_object[this.client.supplier_type] =
            this.client.supplier_id;
          this.$store.state.pageName = this.client.client_name;
          this.activityForm.model_id = this.client.id;
          this.supplierEmployees =
            this.client.supplier_type === 'App\\Client'
              ? this.client.supplier.employees_without_pivot
              : this.client.supplier.employees;
          this.isCompanyContactsLoading = false;
          this.loadingEmployees = false;
          this.loadingActivities = false;
          this.client.filter_groups = response.data.client_filter_groups;
          this.client_groups = response.data.client_filter_groups.map(
            (group) => group.data
          );
        } else {
          this.snackbarMessage = this.langMap.main.generic_error;
          this.actionColor = 'error';
          this.snackbar = true;
          this.isCompanyContactsLoading = false;
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
          this.getClient();
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
            this.getClient();
          } else {
            this.snackbarMessage = this.langMap.main.generic_error;
            this.actionColor = 'error';
            this.snackbar = true;
          }
        });
    },
    createInternalBilling() {
      this.internalBillingForm.entity_id = this.client.id;
      this.internalBillingForm.entity_type = 'App\\Client';
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
            this.getClient();
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

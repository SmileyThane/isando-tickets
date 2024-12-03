<template>
  <v-container fluid>
    <v-snackbar
      v-model="snackbar"
      :bottom="true"
      :color="actionColor"
      :right="true"
    >
      {{ snackbarMessage }}
    </v-snackbar>
    <div class="row justify-content-center">
        <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <v-data-table
              :footer-props="footerProps"
              :headers="headers"
              :items="externalSources"
              :loading="loading"
              :loading-text="langMap.main.loading"
              :options.sync="options"
              :server-items-length="totalCustomers"
              class="elevation-1"
              fixed-header
              hide-default-footer
            >
              <template v-slot:top>
                <v-row>
                  <v-col md="10" sm="12">
                    <v-btn
                      style="margin-left: 10px"
                      outlined
                      :color="themeBgColor"
                      @click="
                        addOrEditExternalSourceDialogOpen = true;
                        dialogMode = 'add';
                      "
                      >{{ langMap.domains.add_new }}
                    </v-btn>
                  </v-col>
                  <v-col md="2" sm="12">
                    <v-select
                      v-model="options.itemsPerPage"
                      :color="themeBgColor"
                      :item-color="themeBgColor"
                      :items="footerProps.itemsPerPageOptions"
                      :label="langMap.main.items_per_page"
                      class="mx-4"
                      @change="updateItemsCount"
                    ></v-select>
                  </v-col>
                </v-row>
              </template>
              <template v-slot:item.domain="{ item }">
                <a :style="`color: ${themeBgColor}`" :href="item.domain_prefix + '://' + item.domain + '.' + item.uri" target="_blank">{{
                  item.domain_prefix + '://' + item.domain + '.' + item.uri
                }}</a>
              </template>
                <template v-slot:item.actions="{ item }">
                    <v-tooltip v-if="item.is_otp_verified" top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                :color="themeBgColor"
                                icon
                                fab
                                x-small
                                dark
                                v-on="on"
                                v-bind="attrs"
                                @click="getAuthenticateCode(item.id)"
                            >
                                <v-icon>mdi mdi-refresh</v-icon>
                            </v-btn>
                        </template>
                        <span>{{ langMap.domains.refresh_auth_code }}</span>
                    </v-tooltip>


                    <v-tooltip v-else top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                :color="themeBgColor"
                                icon
                                fab
                                x-small
                                dark
                                v-on="on"
                                v-bind="attrs"
                                @click="otpDialog = true; externalSourceForm = item;"
                            >
                                <v-icon>mdi mdi-download</v-icon>
                            </v-btn>
                        </template>
                        <span>{{ langMap.domains.get_auth_code }}</span>
                    </v-tooltip>

                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                :color="themeBgColor"
                                icon
                                fab
                                x-small
                                dark
                                v-on="on"
                                v-bind="attrs"
                                @click="
                    addOrEditExternalSourceDialogOpen = true;
                    dialogMode = 'edit';
                    externalSourceForm = item;
                  "
                            >
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                        </template>
                        <span>{{ langMap.domains.edit }}</span>
                    </v-tooltip>

                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                color="error"
                                icon
                                fab
                                x-small
                                dark
                                v-on="on"
                                v-bind="attrs"
                                @click="
                    removeExternalSourceDialogOpen = true;
                    dialogMode = 'edit';
                    externalSourceForm = item;
                  "
                            >
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </template>
                        <span>{{ langMap.domains.remove }}</span>
                    </v-tooltip>

                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                color="primary"
                                icon
                                fab
                                x-small
                                dark
                                v-on="on"
                                v-bind="attrs"
                                @click="handleCopyPassword(item.id)"
                            >
                                <v-icon>mdi mdi-content-copy</v-icon>
                            </v-btn>
                        </template>
                        <span>{{ langMap.domains.copy_password }}</span>
                    </v-tooltip>
                </template>
              <template v-slot:footer>
                <v-pagination
                  v-model="options.page"
                  :color="themeBgColor"
                  :length="lastPage"
                  :page="options.page"
                  :total-visible="5"
                  circle
                >
                </v-pagination>
              </template>
            </v-data-table>
          </div>
        </div>
      </div>
    </div>

    <v-dialog
      v-model="addOrEditExternalSourceDialogOpen"
      max-width="840"
      persistent
    >
      <v-card>
        <v-card-title
          :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`"
          class="mb-5"
        >
          {{
            dialogMode === 'add'
              ? langMap.domains.add_new_domain
              : langMap.domains.edit_domain
          }}
        </v-card-title>
        <v-card-text>
          <v-form
            ref="externalSourceForm"
            v-model="isFormValid"
            lazy-validation
          >
            <!-- Domain Section -->
            <v-card elevation="1" outlined style="padding: 6px 18px">
              <v-card-title>{{ langMap.domains.domain }}</v-card-title>
              <v-card-actions>
                <v-row>
                  <v-col cols="6" md="4">
                    <v-text-field
                      v-model="externalSourceForm.domain_prefix"
                      :color="themeBgColor"
                      :label="langMap.domains.domain_prefix"
                      :rules="[requiredRule]"
                      dense
                      required
                    />
                  </v-col>
                    <v-col cols="6" md="4">
                        <v-text-field
                            v-model="externalSourceForm.domain"
                            :color="themeBgColor"
                            :label="langMap.domains.domain"
                            :rules="[requiredRule]"
                            dense
                            required
                        />
                    </v-col>
                  <v-col cols="6" md="4">
                    <v-text-field
                      v-model="externalSourceForm.uri"
                      :color="themeBgColor"
                      :label="langMap.domains.uri"
                      :rules="[requiredRule]"
                      dense
                      required
                    />
                  </v-col>
                </v-row>
              </v-card-actions>
            </v-card>

            <v-divider class="divider">&nbsp;</v-divider>

            <!-- Auth Section -->
            <v-card elevation="1" outlined style="padding: 6px 18px">
              <v-card-title>{{ langMap.domains.auth }}</v-card-title>
              <v-card-actions>
                <v-row>
                  <v-col cols="6" md="6">
                    <v-text-field
                      v-model="externalSourceForm.auth_name"
                      :color="themeBgColor"
                      :label="langMap.domains.auth_name"
                      :rules="[requiredRule]"
                      dense
                      required
                      class="mb-4"
                    />
                    <v-select
                      v-model="externalSourceForm.auth_method"
                      :color="themeBgColor"
                      :items="['GET', 'POST']"
                      :label="langMap.domains.auth_method"
                      :rules="[requiredRule]"
                      dense
                      required
                    />
                  </v-col>
                  <v-col cols="6" md="6">
                    <v-textarea
                      v-model="externalSourceForm.auth_headers"
                      :color="themeBgColor"
                      :label="langMap.domains.auth_headers"
                      :rules="[requiredRule]"
                      class="form_textarea"
                      dense
                      required
                    />
                  </v-col>
                </v-row>
              </v-card-actions>
            </v-card>

            <v-divider class="divider">&nbsp;</v-divider>

            <!-- Credentials Section -->
            <v-card elevation="1" outlined style="padding: 6px 18px">
              <v-card-title
                >{{ langMap.domains.credentials }}
              </v-card-title>
              <v-card-actions>
                <v-row>
                  <v-col cols="6" md="4">
                    <v-text-field
                      v-model="externalSourceForm.name"
                      :color="themeBgColor"
                      :label="langMap.domains.name"
                      :rules="[requiredRule]"
                      dense
                      required
                    />
                  </v-col>
                  <v-col cols="6" md="4">
                    <v-text-field
                      v-model="externalSourceForm.password"
                      :color="themeBgColor"
                      :label="langMap.domains.password"
                      :rules="[requiredRule]"
                      dense
                      required
                    />
                  </v-col>
                </v-row>
              </v-card-actions>
            </v-card>

            <v-divider class="divider">&nbsp;</v-divider>

            <!-- Billing Section -->
            <v-card elevation="1" outlined style="padding: 6px 18px">
              <v-card-title>{{ langMap.domains.billing }}</v-card-title>
              <v-card-actions>
                <v-row>
                  <v-col cols="6" md="3">
                    <v-text-field
                      v-model="externalSourceForm.billing_price"
                      :color="themeBgColor"
                      :label="langMap.domains.billing_price"
                      :rules="[requiredRule]"
                      dense
                      required
                      type="number"
                    />
                  </v-col>
                  <v-col cols="6" md="3">
                    <v-select
                      v-model="externalSourceForm.billing_currency"
                      :color="themeBgColor"
                      :items="currencies"
                      item-value="id"
                      item-text="slug"
                      :label="langMap.domains.billing_currency"
                      :rules="[requiredRule]"
                      dense
                      required
                    />
                  </v-col>
                  <v-col cols="6" md="3">
                    <v-select
                      v-model="externalSourceForm.billing_type"
                      :color="themeBgColor"
                      :items="billing_type_items"
                      item-text="label"
                      item-value="value"
                      :label="langMap.domains.billing_type"
                      :rules="[requiredRule]"
                      dense
                      required
                    />
                  </v-col>
                  <v-col cols="6" md="3">
                    <v-select
                      v-model="externalSourceForm.is_billing_auto_renewal"
                      :color="themeBgColor"
                      :items="billing_auto_renewal_items"
                      item-value="value"
                      item-text="label"
                      :label="langMap.domains.is_billing_auto_renewal"
                      :disabled="externalSourceForm.billing_type !== 2"
                      dense
                      required
                    />
                  </v-col>
                </v-row>
              </v-card-actions>
            </v-card>
          </v-form>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            :disabled="!isFormValid"
            color="grey darken-1"
            text
            @click="submitForm"
          >
            {{
              dialogMode === 'add'
                ? langMap.domains.add
                : langMap.domains.save
            }}
          </v-btn>
          <v-btn
            color="red darken-1"
            text
            @click="addOrEditExternalSourceDialogOpen = false"
          >
            {{ langMap.domains.close }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog
      v-model="removeExternalSourceDialogOpen"
      max-width="580"
      persistent
    >
      <v-card>
        <v-card-title
          :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`"
          class="mb-5"
        >
          {{ langMap.domains.delete_confirmation }}
        </v-card-title>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="grey darken-1"
            text
            @click="removeExternalSourceDialogOpen = false"
          >
            {{ langMap.main.no }}
          </v-btn>
          <v-btn color="red darken-1" text @click="removeExternalSource">
            {{ langMap.main.yes }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

      <v-dialog
          v-model="otpDialog"
          max-width="580"
          persistent
      >
          <v-card>
              <v-card-title
                  :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`"
                  class="mb-5"
              >
                  <span>{{ langMap.domains.authentication }}</span>
              </v-card-title>
              <v-card-actions>
                  <div style="width: 100%; display: flex; flex-direction: column;">
                      <v-text-field v-model="otpSecret" placeholder="Enter your OTP secret"/>
                      <div style="width: 100%; display: flex; justify-content: space-between;">
                          <v-spacer></v-spacer>
                          <v-btn
                              color="grey darken-1"
                              text
                              @click="otpDialog = false"
                          >
                              {{ langMap.main.close }}
                          </v-btn>
                          <v-btn color="red darken-1" text @click="setOtpSecret(externalSourceForm.id)">
                              {{ langMap.domains.connect }}
                          </v-btn>
                      </div>
                  </div>
              </v-card-actions>
          </v-card>
      </v-dialog>
  </v-container>
</template>

<style>
.divider {
  margin: 16px 0;
}

.form_textarea textarea {
  height: 86px;
}
</style>

<script>
import EventBus from '../../components/EventBus.vue';

export default {
  props: {
    type: {
      type: Number,
      required: true,
      default: 1,
    },
  },
  data() {
    return {
      snackbar: false,
      actionColor: '',
      snackbarMessage: '',
      themeFgColor: this.$store.state.themeFgColor,
      themeBgColor: this.$store.state.themeBgColor,
      totalCustomers: 0,
      lastPage: 0,
      loading: this.themeBgColor,
      langMap: this.$store.state.lang.lang_map,
      options: {
        page: 1,
        sortDesc: [false],
        sortBy: ['name'],
        itemsPerPage: localStorage.itemsPerPage
          ? parseInt(localStorage.itemsPerPage)
          : 10,
      },
      footerProps: {
        showFirstLastPage: true,
        itemsPerPageOptions: [
          {
            text: 10,
            value: 10,
          },
          {
            text: 20,
            value: 20,
          },
          {
            text: 50,
            value: 50,
          },
          {
            text: 100,
            value: 100,
          },
          {
            text: this.$store.state.lang.lang_map.sidebar.all,
            value: 10000,
          },
        ],
      },
      headers: [
        {
          text: this.$store.state.lang.lang_map.domains.id,
          value: 'id',
        },
        {
          text: this.$store.state.lang.lang_map.domains.name,
          value: 'name',
        },
        {
          text: this.$store.state.lang.lang_map.domains.domain,
          value: 'domain',
          sortable: false,
        },
        {
          text: this.$store.state.lang.lang_map.domains.billing_price,
          value: 'billing_price',
        },
        {
          text: this.$store.state.lang.lang_map.domains
            .billing_currency,
          value: 'billing_currency',
        },
        {
          text: this.$store.state.lang.lang_map.domains
            .otp_code,
          value: 'otp_code'
        },
        {
          text: '',
          value: 'actions',
        },
      ],
      externalSources: [],
      name: '',
      externalSourceForm: {
        id: null,
        domain: '',
        domain_prefix: '',
        uri: '',
        name: '',
        auth_name: '',
        auth_method: '',
        auth_headers: '',
        password: '',
        billing_price: null,
        billing_currency: null,
        billing_type: null,
        is_billing_auto_renewal: false,
        external_source_type_id: null,
      },
      billing_type_items: [
        {
          label: this.$store.state.lang.lang_map.domains.payment,
          value: 1,
        },
        {
          label: this.$store.state.lang.lang_map.domains.subscription,
          value: 2,
        },
      ],
      billing_auto_renewal_items: [
        { label: 'True', value: true },
        { label: 'False', value: false },
      ],
      currencies: [],
      addOrEditExternalSourceDialogOpen: false,
      removeExternalSourceDialogOpen: false,
      dialogMode: 'add',
      isFormValid: false,
      requiredRule: (value) => !!value || this.$store.state.lang.lang_map.domains.required_field,
      otpSecret: '',
      otpDialog: false,
    };
  },
  mounted() {
    this.name = this.$route.name;
    this.getExternalSources(this.type);
    this.getCurrency();
    let that = this;
    EventBus.$on('update-theme-color', function (color) {
      that.themeBgColor = color;
    });
  },
  watch: {
    'externalSourceForm.billing_type': function (newVal) {
      if (newVal === 1) {
        this.externalSourceForm.is_billing_auto_renewal = false;
      }
    },
    addOrEditExternalSourceDialogOpen(value) {
      if (!value) {
        this.cleanExternalSourceForm();
      }
    },
  },
  methods: {
      getAuthenticateCode(id){
          axios.get(`/api/external-sources/${id}/get-otp`).then((response) => {
            this.externalSources = this.externalSources.map((el) => el.id === id ? {...el, otp_code: response.data.data.otp} : el);
          })
      },
      setOtpSecret(id){
          axios.post(`/api/external-sources/${id}/set-otp-secret`, {otp_secret: this.otpSecret}).then((response) => {
              if(response.data.success) this.otpDialog = false
              this.getExternalSources()
          })
      },
    submitForm() {
      if (this.$refs.externalSourceForm.validate()) {
        this.saveOrAddExternalSource();
      }
    },
    cleanExternalSourceForm() {
      this.externalSourceForm = {
        id: null,
        domain: '',
        domain_prefix: '',
        uri: '',
        name: '',
        auth_name: '',
        auth_method: '',
        auth_headers: '',
        password: '',
        billing_price: null,
        billing_currency: null,
        billing_type: null,
        is_billing_auto_renewal: false,
        external_source_type_id: null,
      };
    },
    getCurrency() {
      axios.get('/api/currencies').then((response) => {
        if (response.data.success) {
          this.currencies = response.data.data;
        }
      });
    },
    getExternalSources() {
      this.loading = this.themeBgColor;
      axios
        .get(`/api/external-sources`, {
          params: {
            type_id: this.type,
          },
        })
        .then((response) => {
          this.loading = false;
          if (response.data.success) {
            this.totalCustomers = response.data.data.total;
            this.lastPage = response.data.data.last_page;
            this.externalSources = response.data.data.data;
          } else {
            this.snackbarMessage =
              this.$store.state.lang.lang_map.main.generic_error;
            this.snackbar = true;
          }
        });
    },
    saveOrAddExternalSource() {
      const formValues = {
        ...this.externalSourceForm,
          external_source_type_id: this.type,
        auth_headers: JSON.stringify(this.externalSourceForm.auth_headers),
      };

      if (this.dialogMode === 'add') {
        axios.post('/api/external-sources', formValues).then((response) => {
          if (response.data.success) {
            this.addOrEditExternalSourceDialogOpen = false;
            this.getExternalSources();
          }
        });
      } else {
        axios
          .patch(
            `/api/external-sources/${this.externalSourceForm.id}`,
            formValues
          )
          .then((response) => {
            if (response.data.success) {
              this.addOrEditExternalSourceDialogOpen = false;
              this.getExternalSources();
            }
          });
      }
    },
    removeExternalSource() {
      axios
        .delete(`/api/external-sources/${this.externalSourceForm.id}`)
        .then((response) => {
          if (response.data.success) {
            this.removeExternalSourceDialogOpen = false;
            this.getExternalSources();
            this.cleanExternalSourceForm();
          }
        });
    },
    updateItemsCount(value) {
      this.options.itemsPerPage = value;
      localStorage.itemsPerPage = value;
      this.options.page = 1;
    },
      handleCopyPassword(id){
        axios.get(`/api/external-sources/${id}/get-password/`).then((response) => {
            if(response.data.success){
                this.snackbarMessage = this.$store.state.lang.lang_map.domains.copied_password;
                this.snackbar = true;
                this.actionColor = 'success';


                // eval(response.data);
                this.copyText(response.data.data);
            }
        })
      },
      copyText(text) {
          const textArea = document.createElement('textarea');
          textArea.value = text;
          document.body.appendChild(textArea);
          textArea.select();
          try {
              document.execCommand('copy');
              console.log('Text copied to clipboard');
          } catch (err) {
              console.error('Unable to copy', err);
          }
          document.body.removeChild(textArea);
      }
  },
};
</script>

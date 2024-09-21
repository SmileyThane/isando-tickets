<template>
  <v-container fluid>
    <v-snackbar
      v-model="snackbar"
      :bottom="true"
      :color="actionColor"
      :right="true"
    >
      {{ name }}
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
                      >Add new
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
                <span>{{
                  item.domain_prefix + '://' + item.domain + '.' + item.uri
                }}</span>
              </template>
              <template v-slot:item.actions="{ item }">
                <v-btn
                  :color="themeBgColor"
                  icon
                  fab
                  x-small
                  dark
                  @click="
                    addOrEditExternalSourceDialogOpen = true;
                    dialogMode = 'edit';
                    externalSourceForm = item;
                  "
                >
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
                <v-btn
                  color="error"
                  icon
                  fab
                  x-small
                  dark
                  @click="
                    removeExternalSourceDialogOpen = true;
                    dialogMode = 'edit';
                    externalSourceForm = item;
                  "
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
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
              ? 'Add new external source'
              : 'Edit external source'
          }}
        </v-card-title>
        <v-card-text>
          <v-card elevation="1" outlined style="padding: 6px 18px">
            <v-card-title>Domain</v-card-title>
            <v-card-actions>
              <v-row>
                <v-col cols="6" md="4">
                  <v-text-field
                    v-model="externalSourceForm.domain"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
                    :label="'Domain'"
                    dense
                    required
                  />
                </v-col>
                <v-col cols="6" md="4">
                  <v-text-field
                    v-model="externalSourceForm.domain_prefix"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
                    :label="'Domain prefix'"
                    dense
                  />
                </v-col>
                <v-col cols="6" md="4">
                  <v-text-field
                    v-model="externalSourceForm.uri"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
                    :label="'Uri'"
                    dense
                  />
                </v-col>
              </v-row>
            </v-card-actions>
          </v-card>

          <v-divider class="divider">&nbsp;</v-divider>

          <v-card elevation="1" outlined style="padding: 6px 18px">
            <v-card-title>Auth</v-card-title>
            <v-card-actions>
              <v-row>
                <v-col cols="6" md="4">
                  <v-text-field
                    v-model="externalSourceForm.auth_name"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
                    :label="'auth_name'"
                    dense
                  />
                </v-col>
                <v-col cols="6" md="4">
                  <v-text-field
                    v-model="externalSourceForm.auth_method"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
                    :label="'auth_method'"
                    dense
                  />
                </v-col>
                <v-col cols="6" md="4">
                  <v-text-field
                    v-model="externalSourceForm.auth_headers"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
                    :label="'auth_headers'"
                    dense
                  />
                </v-col>
              </v-row>
            </v-card-actions>
          </v-card>

          <v-divider class="divider">&nbsp;</v-divider>

          <v-card elevation="1" outlined style="padding: 6px 18px">
            <v-card-title>Credentials</v-card-title>
            <v-card-actions>
              <v-row>
                <v-col cols="6" md="4">
                  <v-text-field
                    v-model="externalSourceForm.name"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
                    :label="'name'"
                    dense
                  />
                </v-col>
                <v-col cols="6" md="4">
                  <v-text-field
                    v-model="externalSourceForm.password"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
                    :label="'password'"
                    dense
                  />
                </v-col>
                <v-col cols="6" md="4">
                  <v-select
                    v-model="externalSourceForm.is_billing_auto_renewal"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
                    :items="[true, false]"
                    :label="'is_billing_auto_renewal'"
                    dense
                  />
                </v-col>
              </v-row>
            </v-card-actions>
          </v-card>

          <v-divider class="divider">&nbsp;</v-divider>

          <v-card elevation="1" outlined style="padding: 6px 18px">
            <v-card-title>Billing</v-card-title>
            <v-card-actions>
              <v-row>
                <v-col cols="6" md="4">
                  <v-text-field
                    v-model="externalSourceForm.billing_price"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
                    :label="'billing_price'"
                    dense
                  />
                </v-col>
                <v-col cols="6" md="4">
                  <v-text-field
                    v-model="externalSourceForm.billing_currency"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
                    :label="'billing_currency'"
                    dense
                  />
                </v-col>
                <v-col cols="6" md="4">
                  <v-text-field
                    v-model="externalSourceForm.billing_type"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
                    :label="'billing_type'"
                    dense
                  />
                </v-col>
              </v-row>
            </v-card-actions>
          </v-card>

          <v-divider class="divider">&nbsp;</v-divider>

          <v-card elevation="1" outlined style="padding: 6px 18px">
            <v-card-title>Entity</v-card-title>
            <v-card-actions>
              <v-row>
                <v-col cols="6" md="4">
                  <v-text-field
                    v-model="externalSourceForm.external_source_type_id"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
                    :label="'external_source_type_id'"
                    dense
                  />
                </v-col>
                <v-col cols="6" md="4">
                  <v-text-field
                    v-model="externalSourceForm.entity_id"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
                    :label="'entity_id'"
                    dense
                  />
                </v-col>
                <v-col cols="6" md="4">
                  <v-text-field
                    v-model="externalSourceForm.entity_type"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
                    :label="'entity_type'"
                    dense
                  />
                </v-col>
              </v-row>
            </v-card-actions>
          </v-card>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="grey darken-1" text @click="saveOrAddExternalSource">
            {{ dialogMode === 'add' ? 'Add' : 'Save' }}
          </v-btn>
          <v-btn
            color="red darken-1"
            text
            @click="addOrEditExternalSourceDialogOpen = false"
          >
            Close
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
          Are you sure you want to delete this source?
        </v-card-title>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="grey darken-1"
            text
            @click="
              saveForLaterFlag = false;
              saveForLater();
            "
          >
            {{ langMap.main.no }}
          </v-btn>
          <v-btn color="red darken-1" text @click="removeExternalSource">
            {{ langMap.main.yes }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<style>
.divider {
  margin: 16px 0;
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
        { text: 'ID', value: 'id' },
        { text: 'Name', value: 'name' },
        { text: 'Domain', value: 'domain', sortable: false },
        {
          text: 'Billing price',
          value: 'billing_price',
        },
        {
          text: 'Currency',
          value: 'billing_currency',
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
        entity_id: null,
        entity_type: '1',
      },
      addOrEditExternalSourceDialogOpen: false,
      removeExternalSourceDialogOpen: false,
      dialogMode: 'add',
    };
  },
  mounted() {
    this.name = this.$route.name;
    this.getExternalSources(this.type);
    let that = this;
    EventBus.$on('update-theme-color', function (color) {
      that.themeBgColor = color;
    });
  },
  watch: {
    addOrEditExternalSourceDialogOpen(value) {
      if (!value) {
        this.cleanExternalSourceForm();
      }
    },
  },
  methods: {
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
        entity_id: null,
        entity_type: '1',
      };
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
            // this.errorType = 'error';
            this.snackbar = true;
          }
        });
    },
    saveOrAddExternalSource() {
      const formValues = {
        ...this.externalSourceForm,
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
            `/api/external-sources?${this.externalSourceForm.id}`,
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
        .delete(`/api/external-sources?${this.externalSourceForm.id}`)
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
  },
};
</script>

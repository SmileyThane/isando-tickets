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
                    <div class="card-header">
                        <v-toolbar
                            :color="themeBgColor"
                            dark
                            dense
                            flat
                        >
                            <v-toolbar-title :style="`color: ${themeFgColor};`">{{
                                    langMap.main.roles
                                }}
                            </v-toolbar-title>
                            <v-spacer></v-spacer>
                            <v-icon v-if="!canBeEdited && $helpers.auth.checkPermissionByIds([35])"
                                    :color="themeFgColor" @click="canBeEdited = true">mdi-pencil
                            </v-icon>
                            <v-btn v-if="canBeEdited" color="white" style="color: black; margin-right: 10px"
                                   @click="canBeEdited = false">
                                {{ langMap.main.cancel }}
                            </v-btn>
                            <v-btn v-if="canBeEdited" color="white" style="color: black;" @click="updateRoles">
                                {{ langMap.main.update }}
                            </v-btn>

                        </v-toolbar>
                    </div>

                    <div class="card-body">
                        <v-data-table
                            :footer-props="footerProps"
                            :headers="headers"
                            :items="items"
                            :items-per-page="10"
                            :loading="loading"
                            :loading-text="langMap.main.loading"
                            class="elevation-1"
                            dense
                        >
                            <template v-slot:top>
                                <v-row>
                                    <v-col sm="12" md="6">
                                        <v-text-field @input="debounceGetFullRoles" v-model="permissionsSearch"
                                                      :color="themeBgColor"
                                                      :label="langMap.roles.search_by_permissions"
                                                      class="mx-4"></v-text-field>
                                    </v-col>
                                    <v-col sm="12" md="6">
                                        <v-text-field @input="debounceGetRoles" v-model="rolesSearch"
                                                      :color="themeBgColor"
                                                      :label="langMap.roles.search_by_roles"
                                                      class="mx-4"></v-text-field>
                                    </v-col>
                                </v-row>
                            </template>
                            <template v-slot:body="props">
                                <tr v-for="index in props.items">

                                    <td
                                        v-for="header in headers"
                                        class="pa-3"
                                    >
                                        <v-btn
                                            v-if="header.value !== '0'"
                                            icon
                                            @click="index[header.value] = canBeEdited ? !index[header.value] : index[header.value]"
                                        >
                                            <v-icon
                                                :color="index[header.value] ? 'green' :'red'"
                                            >
                                                {{ index[header.value] ? 'mdi-check-circle-outline' : 'mdi-cancel' }}
                                            </v-icon>
                                        </v-btn>
                                        {{ header.value === '0' ? index[header.value]['name'] : '' }}
                                    </td>
                                </tr>
                            </template>

                        </v-data-table>
                    </div>
                </div>
            </div>
            <v-col cols="12">
                <v-btn
                    :color="themeBgColor"
                    :style="`color: ${themeFgColor};`"
                    v-text="langMap.roles.create_new_role"
                    @click="createRoleDialog = true"
                />
                <v-btn
                    :color="themeBgColor"
                    :style="`color: ${themeFgColor};`"
                    v-text="langMap.roles.clone_role"
                    @click="cloneRoleDialog = true"
                />
            </v-col>
        </div>
        <v-dialog v-model="createRoleDialog" max-width="600px" persistent>
            <v-card dense outlined>
                <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                    {{ langMap.roles.create_new_role }}
                </v-card-title>
                <v-card-text>
                    <v-container>
                        <v-row>
                            <v-col cols="12">
                                <v-text-field
                                    v-model="forms.createRole.name"
                                    :color="themeBgColor"
                                    :item-color="themeBgColor"
                                    :label="langMap.roles.role_name_label"
                                    dense
                                />
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-btn
                        color="red"
                        @click="createRoleDialog=false"
                        :style="`color: ${themeFgColor};`"
                        v-text="langMap.main.cancel"
                    />
                    <v-btn
                        :color="themeBgColor"
                        :style="`color: ${themeFgColor};`"
                        v-text="langMap.main.create"
                        @click="createRoleDialog=false; createRole(forms.createRole)"
                    />
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="cloneRoleDialog" max-width="600px" persistent>
            <v-card dense outlined>
                <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                    {{ langMap.roles.clone_role }}
                </v-card-title>
                <v-card-text>
                    <v-container>
                        <v-row>
                            <v-col cols="12">
                                <v-text-field
                                    v-model="forms.cloneRole.name"
                                    :color="themeBgColor"
                                    :item-color="themeBgColor"
                                    :label="langMap.roles.role_name_label"
                                    dense
                                />
                            </v-col>
                            <v-col cols="12">
                                <v-select
                                    v-model="forms.cloneRole.role_id"
                                    :items="roles"
                                    item-text="name"
                                    item-value="id"
                                    :label="langMap.roles.select_role_to_clone"
                                    dense
                                ></v-select>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-btn
                        color="red"
                        @click="cloneRoleDialog=false"
                        :style="`color: ${themeFgColor};`"
                        v-text="langMap.main.cancel"
                    />
                    <v-btn
                        :color="themeBgColor"
                        :style="`color: ${themeFgColor};`"
                        v-text="langMap.main.clone"
                        @click="cloneRoleDialog=false; cloneRole(forms.cloneRole)"
                    />
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>


<script>
import EventBus from "../../components/EventBus";

export default {

    data() {
        return {
            snackbar: false,
            snackbarMessage: '',
            actionColor: '',
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            loading: this.themeBgColor,
            langMap: this.$store.state.lang.lang_map,
            headers: [],
            items: [],
            footerProps: {
                showFirstLastPage: true,
                itemsPerPageOptions: [5, 10],
            },
            canBeEdited: false,
            permissionsSearch: '',
            rolesSearch: '',
            createRoleDialog: false,
            cloneRoleDialog: false,
            roles: [],
            forms: {
                createRole: {
                    name: '',
                },
                cloneRole: {
                    name: '',
                    role_id: null,
                }
            }
        }
    },
    mounted() {
        let that = this;
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
        this.getRoles()
        this.getFullRoles()
    },
    created() {
        this.debounceGetFullRoles = _.debounce(this.getFullRoles, 1000);
        this.debounceGetRoles = _.debounce(this.getRoles, 1000);
    },
    methods: {
        createRole(data) {
            this.$store.dispatch('Roles/createRole', data)
                .then(result => {
                    console.log('result');
                    if (result) {
                        this.getRoles();
                        this.getFullRoles();
                        this.showSnackbar(this.langMap.roles.role_created);
                    }
                })
                .catch(error => {
                    this.showSnackbar(this.langMap.main.generic_error, 'error');
                });
        },
        cloneRole(data) {
            this.$store.dispatch('Roles/cloneRole', data)
                .then(result => {
                    if (result) {
                        this.getRoles();
                        this.getFullRoles();
                        this.showSnackbar(this.langMap.roles.role_cloned);
                    }
                })
                .catch(error => {
                    this.showSnackbar(this.langMap.main.generic_error, 'error');
                });
        },
        showSnackbar(text, color = 'success') {
            this.snackbarMessage = text;
            this.actionColor = color;
            this.snackbar = true;
        },
        getFullRoles() {
            this.loading = this.themeBgColor
            axios.get('/api/roles/full', {
                params: {
                    search: this.permissionsSearch,
                }
            }).then(response => {
                this.loading = false
                response = response.data
                if (response.success === true) {
                    this.items = response.data
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        getRoles() {
            this.loading = this.themeBgColor
            let defaultOption = [{'text': 'permissions', 'value': "0", sortable: false}];
            axios.get('/api/roles', {
                params: {
                    search: this.rolesSearch,
                }
            }).then(response => {
                this.loading = false
                response = response.data
                if (response.success === true) {
                    this.roles = response.data;
                    let result = response.data.map((item) => {
                        return {'text': item.name, 'value': `${item.id}`, sortable: false};
                    })
                    this.headers = defaultOption.concat(result)
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        updateRoles() {
            this.loading = this.themeBgColor;
            axios.put(`/api/roles/full`, {'data': this.items}).then(response => {
                this.loading = false;
                response = response.data;
                if (response.success === true) {
                    this.items = response.data;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
            this.canBeEdited = false;
        }
    },
    watch: {},
}
</script>

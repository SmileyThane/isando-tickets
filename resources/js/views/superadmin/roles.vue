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
                            <v-icon v-if="!canBeEdited && $helpers.auth.checkPermissionByIds([35])" :color="themeFgColor" @click="canBeEdited = true">mdi-pencil
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
                            :headers="headers"
                            :items="items"
                            :items-per-page="10"
                            :loading="loading"
                            :footer-props="footerProps"
                            :loading-text="langMap.main.loading"
                            class="elevation-1"
                            dense
                        >
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
        </div>
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
            canBeEdited: false
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
    methods: {
        getFullRoles() {
            this.loading = this.themeBgColor
            axios.get('/api/roles/full')
                .then(response => {
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
            axios.get('/api/roles')
                .then(response => {
                    this.loading = false
                    response = response.data
                    if (response.success === true) {
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
            axios.put(`/api/roles/full`, {'data': this.items}).then(response => {
                response = response.data
                if (response.success === true) {
                    this.items = response.data
                    window.location.reload()
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

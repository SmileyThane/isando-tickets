<template>
    <v-container>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"></div>
                    <div class="card-body">
                        <v-expansion-panels>
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    {{ langMap.team.add_new }}
                                    <template v-slot:actions>
                                        <v-icon color="submit">mdi-plus</v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-form>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <v-text-field
                                                    color="green"
                                                    :label="langMap.main.name"
                                                    name="team_name"
                                                    type="text"
                                                    v-model="teamForm.team_name"
                                                    required
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-6">
                                                <v-text-field
                                                    color="green"
                                                    :label="langMap.main.description"
                                                    name="team_description"
                                                    type="text"
                                                    v-model="teamForm.team_description"
                                                    required
                                                ></v-text-field>
                                            </div>
                                            <v-btn
                                                dark
                                                fab
                                                right
                                                bottom
                                                color="green"
                                                @click="addTeam"
                                            >
                                                <v-icon>mdi-plus</v-icon>
                                            </v-btn>
                                        </div>
                                    </v-form>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                        </v-expansion-panels>
                        <v-data-table
                            show-expand
                            :headers="headers"
                            :items="teams"
                            :loading="loading"
                            :footer-props="footerProps"
                            :single-expand="singleExpand"
                            :expanded.sync="expanded"
                            :options.sync="options"
                            :items-per-page="25"
                            class="elevation-1"
                            hide-default-footer
                            :loading-text="langMap.main.loading"
                            @click:row="showItem"
                        >
                            <template v-slot:top>
                                <v-row>
                                    <v-col sm="12" md="10">
                                        <v-text-field @input="getTeams" v-model="teamsSearch" color="green"
                                                      :label="langMap.main.search" class="mx-4"></v-text-field>
                                    </v-col>
                                    <v-col sm="12" md="2">
                                        <v-select
                                            class="mx-4"
                                            color="green"
                                            item-color="green"
                                            :items="footerProps.itemsPerPageOptions"
                                            :label="langMap.main.items_per_page"
                                            @change="updateItemsCount"
                                        ></v-select>
                                    </v-col>
                                </v-row>
                            </template>
                            <template v-slot:footer>
                                <v-pagination color="green"
                                              v-model="options.page"
                                              :length="lastPage"
                                              circle
                                              :page="options.page"
                                              :total-visible="5"
                                >
                                </v-pagination>
                            </template>
                            <template v-slot:expanded-item="{ headers, item }">
                                <td :colspan="headers.length">
                                    <v-spacer>
                                        &nbsp;
                                    </v-spacer>
                                    <p><strong>{{ langMap.main.actions }}:</strong></p>
                                    <p>
                                        <v-btn
                                            color="grey"
                                            dark
                                            @click="showItem(item)"
                                            fab
                                            x-small
                                        >
                                            <v-icon
                                            >
                                                mdi-eye
                                            </v-icon>
                                        </v-btn>

                                        <v-btn
                                            color="error"
                                            dark
                                            @click="deleteProcess(item)"
                                            fab
                                            x-small
                                        >
                                            <v-icon>
                                                mdi-delete
                                            </v-icon>
                                        </v-btn>
                                    </p>
                                </td>
                            </template>
                        </v-data-table>
                    </div>
                </div>
            </div>
        </div>
        <template>
            <v-dialog v-model="removeTeamDialog" persistent max-width="480">
                <v-card>
                    <v-card-title class="headline">{{ langMap.main.delete_selected }}?</v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="removeTeamDialog = false">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn color="red darken-1" text @click="deleteTeam(selectedTeamId)">
                            {{ langMap.main.delete }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
    </v-container>
</template>


<script>
export default {

    data() {
        return {
            snackbar: false,
            actionColor: '',
            snackbarMessage: '',
            totalTeams: 0,
            lastPage: 0,
            loading: 'green',
            expanded: [],
            singleExpand: false,
            langMap: this.$store.state.lang.lang_map,
            options: {
                page: 1,
                sortDesc: [false],
                sortBy: ['id']
            },
            footerProps: {
                itemsPerPage: 10,
                showFirstLastPage: true,
                itemsPerPageOptions: [10, 25, 50, 100],
            },
            headers: [
                {text: '', value: 'data-table-expand'},
                {
                    text: 'ID',
                    align: 'start',
                    sortable: false,
                    value: 'id',
                },
                {text: `${this.$store.state.lang.lang_map.main.name}`, value: 'name'},
                {text: `${this.$store.state.lang.lang_map.main.description}`, value: 'description'},
            ],
            teamsSearch: '',
            teams: [],
            teamForm: {
                team_name: '',
                team_description: '',
            },
            selectedTeamId: null,
            removeTeamDialog: false,
        }
    },
    mounted() {
        this.getTeams()
    },
    methods: {
        getTeams() {
            axios.get(`api/team?
                search=${this.teamsSearch}&
                sort_by=${this.options.sortBy[0]}&
                sort_val=${this.options.sortDesc[0]}&
                per_page=${this.options.itemsPerPage}&
                page=${this.options.page}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.teams = response.data.data
                    this.totalTeams = response.data.total
                    this.lastPage = response.data.last_page
                    this.loading = false
                } else {
                    console.log('error')
                }
            });
        },
        addTeam() {
            axios.post('api/team', this.teamForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getTeams()
                } else {
                    console.log('error')
                }
            });
        },
        showItem(item) {
            this.$router.push(`/team/${item.id}`)
        },
        deleteProcess(item) {
            this.selectedTeamId = item.id
            this.removeTeamDialog = true
        },
        deleteTeam(id) {
            axios.delete(`/api/team/${id}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getTeams()
                    this.snackbarMessage = 'Team was deleted '
                    this.actionColor = 'success'
                    this.snackbar = true;
                    this.removeTeamDialog = false
                } else {
                    this.snackbarMessage = 'Team delete error'
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        updateItemsCount(value) {
            this.options.itemsPerPage = value
            this.options.page = 1
        },
    }
}
</script>

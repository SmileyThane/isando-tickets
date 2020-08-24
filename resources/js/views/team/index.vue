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
                                    Add New Team
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
                                                    label="Name"
                                                    name="team_name"
                                                    type="text"
                                                    v-model="teamForm.team_name"
                                                    required
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-6">
                                                <v-text-field
                                                    color="green"
                                                    label="Description"
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
                            :single-expand="singleExpand"
                            :expanded.sync="expanded"
                            :items-per-page="25"
                            class="elevation-1"
                            @click:row="showItem"
                        >
                            <template v-slot:expanded-item="{ headers, item }">
                                <td :colspan="headers.length">
                                    <v-spacer>
                                        &nbsp;
                                    </v-spacer>
                                    <p><strong>Actions:</strong></p>
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
    </v-container>
</template>


<script>
    export default {

        data() {
            return {
                expanded: [],
                singleExpand: false,
                headers: [
                    {text: '', value: 'data-table-expand'},
                    {
                        text: 'ID',
                        align: 'start',
                        sortable: false,
                        value: 'id',
                    },
                    {text: 'name', value: 'name'},
                    {text: 'Description', value: 'description'},
                ],
                teams: [],
                teamForm: {
                    team_name: '',
                    team_description: '',
                },
            }
        },
        mounted() {
            this.getTeams()
        },
        methods: {
            getTeams() {
                axios.get('api/team').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.teams = response.data.data
                        // console.log(this.customers);
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
            }
        }
    }
</script>

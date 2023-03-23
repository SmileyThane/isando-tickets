<template>
    <v-row
        v-if="$store.getters['IncidentReporting/getRelatedIR'] &&
            $store.getters['IncidentReporting/getRelatedIR'].action_boards.length > 0"
    >
        <v-col cols="12">
            <v-list dense subheader>
                <v-list-item
                    class="mb-1 action-board"
                    v-for="(item, index) in $store.getters['IncidentReporting/getRelatedIR'].action_boards"
                    :key="item.id"
                >
                    <v-list-item-content>
                        <v-list-item-title v-text="$helpers.i18n.localized(item)"></v-list-item-title>
                    </v-list-item-content>
                    <v-list-item-action>
                        <v-icon
                            v-if="actionsList.length === 0"
                            @click="showList(item)"
                        >
                            mdi-eye-outline
                        </v-icon>
                        <v-icon
                            v-else
                            @click="hideList()"
                        >
                            mdi-eye-off-outline
                        </v-icon>
                    </v-list-item-action>
                    <v-list-item-action>
                        <v-icon
                            @click="addToList(item)">
                            mdi-plus
                        </v-icon>
                    </v-list-item-action>
                </v-list-item>
            </v-list>
            <v-btn
                :color="themeBgColor"
                class="mb-1"
                :style="`color: ${themeFgColor}`"
                @click="addAllToList()">
                Add all actions
            </v-btn>
        </v-col>
        <v-col cols="12" v-if="actionsList.length > 0">
            <h4 class="heading">{{ langMap.ir.ab.actions }}</h4>
            <v-simple-table cols="12">
                <template v-slot:default>
                    <thead>
                    <tr>
                        <th class="text-left">
                            {{ langMap.main.name }}
                        </th>
                        <th class="text-left">
                            {{ langMap.ir.ab.deadline }}
                        </th>
                        <th class="text-left">
                            {{ langMap.ir.ab.assigned_to }}
                        </th>
                        <th class="text-left">
                            {{ langMap.main.actions }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr
                        v-for="(item, index) in actionsList"
                        :key="item.id"
                    >
                        <td>{{ item.name }}</td>
                        <td>{{
                                `${item.deadline_time_indicator} ${item.deadline_time_value} ${item.deadline_time_parameter}`
                            }}
                        </td>
                        <td>{{ item.assignee ? item.assignee.name : '' }}</td>
                        <td class="text-center">
                            <v-icon @click="addToList(item)">
                                mdi-plus
                            </v-icon>
                        </td>
                    </tr>
                    </tbody>
                </template>
            </v-simple-table>
        </v-col>
        <v-col cols="12" v-if="actionBoardsList.length > 0">
            <h4 class="heading">{{ langMap.ir.ab.title }}</h4>
            <v-simple-table cols="12">
                <template v-slot:default>
                    <thead>
                    <tr>
                        <th class="text-left">
                            {{ langMap.main.name }}
                        </th>
                        <th class="text-left">
                            {{ langMap.ir.ab.status }}
                        </th>
                        <th class="text-left">
                            {{ langMap.ir.ab.valid_till }}
                        </th>
                        <th class="text-left">
                            {{ langMap.main.actions }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr
                        v-for="(item, index) in actionsList"
                        :key="item.id"
                    >
                        <td>{{ item.name }}</td>
                        <td>{{ item.status ? item.status.name : '' }}</td>
                        <td>{{ item.valid_till }}</td>
                        <td class="text-center">
                            <v-icon @click="addToList(item)">
                                mdi-plus
                            </v-icon>
                        </td>
                    </tr>
                    </tbody>
                </template>
            </v-simple-table>
        </v-col>
    </v-row>
</template>

<script>

export default {
    name: 'incident-list-action-boards',
    data() {
        return {
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            actionsList: [],
            actionBoardsList: [],
            finalList: [],
        }
    },
    mounted() {
        if (this.$route.params.id) {
            this.finalList = this.$store.getters['IncidentReporting/getSelectedIR'].actions
        }
    },
    watch: {
        $route() {
            this.finalList = []
        }
    },
    methods: {
        showList(item) {
            this.actionsList = item.actions ?? []
            this.actionBoardsList = item.action_boards ?? []
        },
        hideList() {
            this.actionsList = [];
        },
        addToList(item) {
            if (item.actions !== undefined && item.actions.length > 0) {
                this.finalList = [...this.finalList, ...item.actions]
            }
            if (item.action_boards !== undefined && item.action_boards.length > 0) {
                for (const key in item.action_boards) {
                    this.finalList = [...this.finalList, ...item.action_boards[key].actions]
                }
            }
            if (item.action_boards === undefined && item.actions === undefined) {
                this.finalList = [...this.finalList, ...[item]]
            }
            this.$store.getters['IncidentReporting/getSelectedIR'].actions = this.finalList

            if (this.$store.getters['IncidentReporting/getSelectedIR'].action_boards.length === 0 ||
                !this.$store.getters['IncidentReporting/getSelectedIR'].action_boards.find(
                    item => item?.id === this.$store.getters['IncidentReporting/getRelatedIR'].id
                )
            ) {
                this.$store.getters['IncidentReporting/getSelectedIR'].action_boards.push(
                    this.$store.getters['IncidentReporting/getRelatedIR']
                );
            }
        },
        addAllToList() {
            this.$store.getters['IncidentReporting/getRelatedIR'].action_boards.map(item => this.addToList(item))
        },
    },
}
</script>

<style scoped>

.heading {
    font-weight: 400;
    letter-spacing: normal;
    font-size: 15px;
    text-align: center;
}

.action-board {
    border: 1px solid #c1c4c9;
    border-radius: 5px;
}

</style>

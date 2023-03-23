<template>
    <v-row>
        <v-col cols="12" lg="8" md="12" sm="12">
            <v-simple-table v-if="actions.length > 0">
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
                    </tr>
                    </thead>
                    <tbody>
                    <tr
                        v-for="(item, index) in actions"
                        :key="item.id"
                    >
                        <td>{{ item.name }}</td>
                        <td>{{
                                `${item.deadline_time_indicator} ${item.deadline_time_value} ${item.deadline_time_parameter}`
                            }}
                        </td>
                        <td>{{ item.assignee ? item.assignee.name : '' }}</td>
                    </tr>
                    </tbody>
                </template>
            </v-simple-table>
            <div v-else class="mt-4">
                <span style="color: grey">{{ langMap.ir.ab.actions_not_found }}</span>
            </div>
        </v-col>
    </v-row>
</template>

<script>

export default {
    name: 'incident-tab-actions',
    data() {
        return {
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
        }
    },
    computed: {
        actions: function () {
            return this.$store.getters['IncidentReporting/getSelectedIR'].actions;
        },
    },
}
</script>

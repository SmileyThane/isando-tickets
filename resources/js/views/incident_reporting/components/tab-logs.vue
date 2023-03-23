<template>
    <v-row>
        <v-col cols="12" lg="8" md="12" sm="12">
            <v-simple-table>
                <template v-slot:default>
                    <thead>
                    <tr>
                        <th class="text-left">
                            {{ langMap.main.name }}
                        </th>
                        <th class="text-right">
                            {{ langMap.ir.ab.date }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr
                        v-for="(item, index) in logs"
                        :key="item.log"
                    >
                        <td>{{ item.log }}</td>
                        <td class="text-right">{{ item.created_at }}</td>
                    </tr>
                    </tbody>
                </template>
            </v-simple-table>
        </v-col>
    </v-row>
</template>

<script>

export default {
    name: 'incident-tab-logs',
    data() {
        return {
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
        }
    },
    computed: {
        logs: function () {
            return this.$store.getters['IncidentReporting/getSelectedIR'].logs;
        },
    },
}
</script>

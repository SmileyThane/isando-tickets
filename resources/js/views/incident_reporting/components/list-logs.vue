<template>
    <v-row
        v-if="$store.getters['IncidentReporting/getSelectedIR'] &&
            $store.getters['IncidentReporting/getSelectedIR'].logs.length > 0"
    >
        <v-col cols="12">
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
                        v-for="(item, index) in $store.getters['IncidentReporting/getSelectedIR'].logs"
                        :key="item.id"
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
    name: 'incident-list-logs',
    data() {
        return {
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
        }
    },
}
</script>

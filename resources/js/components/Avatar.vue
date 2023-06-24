<template>

    <v-tooltip top>
        <template v-slot:activator="{ on, attrs }">
            <v-avatar
                :color="getColor"
            >
                <img
                    v-bind="attrs"
                    v-on="on"
                    v-if="user.avatar_url"
                    :src="user.avatar_url"
                    :alt="user.full_name"
                >
                <span
                    v-else-if="user.full_name"
                    v-bind="attrs"
                    v-on="on"
                    class="white--text headline text-uppercase"
                >
                    {{
                        user.full_name.split(/\s/).reduce((response, word) => response += word.slice(0, 1), '').substr(0, 2).toLocaleUpperCase()
                    }}
                </span>
                <v-icon
                    v-else
                    large
                >
                    mdi-account-circle
                </v-icon>
            </v-avatar>
        </template>
        <span>{{user.full_name}}</span>
    </v-tooltip>
</template>

<script>
export default {
    name: 'avatar',
    props: {
        color: {
            type: String,
            default: 'grey darken-1',
        },
        user: {
            type: Object,
            default: null,
        }
    },
    data() {
        return {}
    },
    created () {},
    mounted () {},
    computed: {
        getColor() {
            if (this.color) {
                return this.color;
            }
            return 'grey darken-1';
        }
    }
}
</script>

<style scoped>

</style>

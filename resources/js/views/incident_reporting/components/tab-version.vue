<template>
    <div>
{{$store.getters['IncidentReporting/getSelectedIR'].version}}
    </div>
</template>

<script>
export default {
    name: 'incident-tab-version',
    data() {
        return {

        }
    }
}
</script>

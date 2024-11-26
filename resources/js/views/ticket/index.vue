<template>
  <tickets
    searchLabel="Company"
    pageName="ticket_list"
    :searchDisabled="false"
  />
</template>

<script>
import tickets from '../../components/tickets/tickets';

export default {
  components: { tickets },

  data() {
    return {};
  },
};
</script>

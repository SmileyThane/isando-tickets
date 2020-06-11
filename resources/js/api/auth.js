export default {

    getTickets() {
        return axios.get('ticket')
    },

    getTicket(id) {
        return axios.get('ticket/' + id)
    },

    addTicket(data) {
        return axios.post('ticket', data)
    },

    editTicket(id, data) {
        return axios.put('ticket/' + id, data)
    },

    deleteTicket(id) {
        return axios.delete('ticket/' + id)
    }
}

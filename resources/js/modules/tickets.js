export default {
    namespaced: true,
    state: {
        tickets: [],
        treeTickets: []
    },
    actions: {
        getTicketList({commit}, { search }) {
            const queryParams = new URLSearchParams({
                search: search ?? ''
            });
            axios.get(`/api/tracking/tickets?${queryParams.toString()}`)
                .then(({ data: { success, data } }) => {
                    commit('GET_TICKETS', data)
                    commit('GET_TREE_TICKETS', data)
                })
        }
    },
    mutations: {
        GET_TICKETS(state, tickets) {
            state.tickets = tickets
        },
        GET_TREE_TICKETS(state, tickets) {
            let companies = [];
            tickets.forEach(i => {
                if (!companies.find(x => x.id === i.from.id)) {
                    companies.push({ ...i.from, tickets: [] })
                }
            })
            companies = companies.map(company => {
                company.tickets = tickets.filter(i => i.from.id === company.id);
                return company;
            })
            state.treeTickets = companies
        }
    },
    getters: {
        getTickets(state) {
            return state.tickets
        },
        getTreeTickets(state) {
            return state.treeTickets
        }
    }
}


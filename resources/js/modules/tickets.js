export default {
    namespaced: true,
    state: {
        tickets: [],
        treeTickets: []
    },
    actions: {
        getTicketList({commit, state}, {search}) {
            if (state.tickets.length && !search) {
                return state.tickets;
            } else {
                const queryParams = new URLSearchParams({
                    search: search ?? '',
                    minified: false,
                    sort_by: 'id',
                    sort_val: true,
                    with_spam: false,
                    per_page: 30,
                    page: 1
                });
                axios.get(`/api/ttmanaging/tickets?${queryParams.toString()}`, {retry: 5, retryDelay: 1000})
                    .then(({data: {success, data: {data}, error}}) => {
                        if (success) {
                            commit('GET_TICKETS', data)
                            commit('GET_TREE_TICKETS', data)
                        }
                    })
            }
        }
    },
    mutations: {
        GET_TICKETS(state, tickets) {
            state.tickets = tickets.filter(i => i.from_entity_id)
        },
        GET_TREE_TICKETS(state, tickets) {
            let companies = [];
            tickets = tickets.filter(i => i.from_entity_id);
            tickets.forEach(i => {
                if (!companies.find(x => x.id === i.from_entity_id) && i.from_entity_name) {
                    companies.push({name: i.from_entity_name, number: i.number, id: i.from_entity_id, tickets: []})
                }
            })
            companies = companies.map(company => {
                company.tickets = tickets.filter(i => i.from_entity_id === company.id);
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


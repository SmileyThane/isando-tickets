import {default as ActionTypes} from './action_types'
import {default as EventTypes} from './event_types'
import {default as FocusPriorities} from './focus_priorities'
import {default as ImpactPotentials} from './impact_potentials'
import {default as ProcessStates} from './process_states'
import {default as ResourceTypes} from './resource_types'
import {default as StakeholderTypes} from './stakeholder_types'
import {default as ActionBoardStatuses} from './action_board_statuses'
import {default as TeamRoles} from './team_roles'

export default {
    namespaced: true,
    modules: {
        ActionTypes,
        EventTypes,
        FocusPriorities,
        ImpactPotentials,
        ProcessStates,
        ResourceTypes,
        StakeholderTypes,
        ActionBoardStatuses,
        TeamRoles
    },
}

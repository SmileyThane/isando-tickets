import { default as ActionTypes } from './action_types'
import { default as EventTypes } from './event_types'
import { default as FocusPriorities } from './focus_priorities'
import { default as ImpactPotentials } from './impact_potentials'
import { default as ProcessStates } from './process_states'
import { default as ResourceTypes } from './resource_types'
import { default as StakeholderTypes } from './stakeholder_types'

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
    },
}

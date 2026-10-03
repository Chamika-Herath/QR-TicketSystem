import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AdminEventController::attendees
* @see app/Http/Controllers/AdminEventController.php:200
* @route '/events/{event}/attendees'
*/
export const attendees = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: attendees.url(args, options),
    method: 'get',
})

attendees.definition = {
    methods: ["get","head"],
    url: '/events/{event}/attendees',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AdminEventController::attendees
* @see app/Http/Controllers/AdminEventController.php:200
* @route '/events/{event}/attendees'
*/
attendees.url = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { event: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { event: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            event: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
    }

    return attendees.definition.url
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminEventController::attendees
* @see app/Http/Controllers/AdminEventController.php:200
* @route '/events/{event}/attendees'
*/
attendees.get = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: attendees.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminEventController::attendees
* @see app/Http/Controllers/AdminEventController.php:200
* @route '/events/{event}/attendees'
*/
attendees.head = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: attendees.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AdminEventController::attendees
* @see app/Http/Controllers/AdminEventController.php:200
* @route '/events/{event}/attendees'
*/
const attendeesForm = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: attendees.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminEventController::attendees
* @see app/Http/Controllers/AdminEventController.php:200
* @route '/events/{event}/attendees'
*/
attendeesForm.get = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: attendees.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminEventController::attendees
* @see app/Http/Controllers/AdminEventController.php:200
* @route '/events/{event}/attendees'
*/
attendeesForm.head = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: attendees.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

attendees.form = attendeesForm

/**
* @see \App\Http\Controllers\AdminEventController::destroyAttendee
* @see app/Http/Controllers/AdminEventController.php:344
* @route '/events/{event}/attendees/{attendee}'
*/
export const destroyAttendee = (args: { event: number | { id: number }, attendee: number | { id: number } } | [event: number | { id: number }, attendee: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyAttendee.url(args, options),
    method: 'delete',
})

destroyAttendee.definition = {
    methods: ["delete"],
    url: '/events/{event}/attendees/{attendee}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AdminEventController::destroyAttendee
* @see app/Http/Controllers/AdminEventController.php:344
* @route '/events/{event}/attendees/{attendee}'
*/
destroyAttendee.url = (args: { event: number | { id: number }, attendee: number | { id: number } } | [event: number | { id: number }, attendee: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            event: args[0],
            attendee: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
        attendee: typeof args.attendee === 'object'
        ? args.attendee.id
        : args.attendee,
    }

    return destroyAttendee.definition.url
            .replace('{event}', parsedArgs.event.toString())
            .replace('{attendee}', parsedArgs.attendee.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminEventController::destroyAttendee
* @see app/Http/Controllers/AdminEventController.php:344
* @route '/events/{event}/attendees/{attendee}'
*/
destroyAttendee.delete = (args: { event: number | { id: number }, attendee: number | { id: number } } | [event: number | { id: number }, attendee: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyAttendee.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\AdminEventController::destroyAttendee
* @see app/Http/Controllers/AdminEventController.php:344
* @route '/events/{event}/attendees/{attendee}'
*/
const destroyAttendeeForm = (args: { event: number | { id: number }, attendee: number | { id: number } } | [event: number | { id: number }, attendee: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyAttendee.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AdminEventController::destroyAttendee
* @see app/Http/Controllers/AdminEventController.php:344
* @route '/events/{event}/attendees/{attendee}'
*/
destroyAttendeeForm.delete = (args: { event: number | { id: number }, attendee: number | { id: number } } | [event: number | { id: number }, attendee: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyAttendee.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroyAttendee.form = destroyAttendeeForm

/**
* @see \App\Http\Controllers\AdminEventController::manualCheckin
* @see app/Http/Controllers/AdminEventController.php:234
* @route '/events/{event}/attendees/{attendee}/manual-checkin'
*/
export const manualCheckin = (args: { event: number | { id: number }, attendee: number | { id: number } } | [event: number | { id: number }, attendee: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: manualCheckin.url(args, options),
    method: 'post',
})

manualCheckin.definition = {
    methods: ["post"],
    url: '/events/{event}/attendees/{attendee}/manual-checkin',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminEventController::manualCheckin
* @see app/Http/Controllers/AdminEventController.php:234
* @route '/events/{event}/attendees/{attendee}/manual-checkin'
*/
manualCheckin.url = (args: { event: number | { id: number }, attendee: number | { id: number } } | [event: number | { id: number }, attendee: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            event: args[0],
            attendee: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
        attendee: typeof args.attendee === 'object'
        ? args.attendee.id
        : args.attendee,
    }

    return manualCheckin.definition.url
            .replace('{event}', parsedArgs.event.toString())
            .replace('{attendee}', parsedArgs.attendee.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminEventController::manualCheckin
* @see app/Http/Controllers/AdminEventController.php:234
* @route '/events/{event}/attendees/{attendee}/manual-checkin'
*/
manualCheckin.post = (args: { event: number | { id: number }, attendee: number | { id: number } } | [event: number | { id: number }, attendee: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: manualCheckin.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AdminEventController::manualCheckin
* @see app/Http/Controllers/AdminEventController.php:234
* @route '/events/{event}/attendees/{attendee}/manual-checkin'
*/
const manualCheckinForm = (args: { event: number | { id: number }, attendee: number | { id: number } } | [event: number | { id: number }, attendee: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: manualCheckin.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AdminEventController::manualCheckin
* @see app/Http/Controllers/AdminEventController.php:234
* @route '/events/{event}/attendees/{attendee}/manual-checkin'
*/
manualCheckinForm.post = (args: { event: number | { id: number }, attendee: number | { id: number } } | [event: number | { id: number }, attendee: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: manualCheckin.url(args, options),
    method: 'post',
})

manualCheckin.form = manualCheckinForm

/**
* @see \App\Http\Controllers\AdminEventController::resendTicket
* @see app/Http/Controllers/AdminEventController.php:266
* @route '/events/{event}/attendees/{attendee}/resend'
*/
export const resendTicket = (args: { event: number | { id: number }, attendee: number | { id: number } } | [event: number | { id: number }, attendee: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resendTicket.url(args, options),
    method: 'post',
})

resendTicket.definition = {
    methods: ["post"],
    url: '/events/{event}/attendees/{attendee}/resend',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminEventController::resendTicket
* @see app/Http/Controllers/AdminEventController.php:266
* @route '/events/{event}/attendees/{attendee}/resend'
*/
resendTicket.url = (args: { event: number | { id: number }, attendee: number | { id: number } } | [event: number | { id: number }, attendee: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            event: args[0],
            attendee: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
        attendee: typeof args.attendee === 'object'
        ? args.attendee.id
        : args.attendee,
    }

    return resendTicket.definition.url
            .replace('{event}', parsedArgs.event.toString())
            .replace('{attendee}', parsedArgs.attendee.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminEventController::resendTicket
* @see app/Http/Controllers/AdminEventController.php:266
* @route '/events/{event}/attendees/{attendee}/resend'
*/
resendTicket.post = (args: { event: number | { id: number }, attendee: number | { id: number } } | [event: number | { id: number }, attendee: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resendTicket.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AdminEventController::resendTicket
* @see app/Http/Controllers/AdminEventController.php:266
* @route '/events/{event}/attendees/{attendee}/resend'
*/
const resendTicketForm = (args: { event: number | { id: number }, attendee: number | { id: number } } | [event: number | { id: number }, attendee: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resendTicket.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AdminEventController::resendTicket
* @see app/Http/Controllers/AdminEventController.php:266
* @route '/events/{event}/attendees/{attendee}/resend'
*/
resendTicketForm.post = (args: { event: number | { id: number }, attendee: number | { id: number } } | [event: number | { id: number }, attendee: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resendTicket.url(args, options),
    method: 'post',
})

resendTicket.form = resendTicketForm

/**
* @see \App\Http\Controllers\AdminEventController::exportCsv
* @see app/Http/Controllers/AdminEventController.php:289
* @route '/events/{event}/export'
*/
export const exportCsv = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportCsv.url(args, options),
    method: 'get',
})

exportCsv.definition = {
    methods: ["get","head"],
    url: '/events/{event}/export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AdminEventController::exportCsv
* @see app/Http/Controllers/AdminEventController.php:289
* @route '/events/{event}/export'
*/
exportCsv.url = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { event: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { event: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            event: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
    }

    return exportCsv.definition.url
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminEventController::exportCsv
* @see app/Http/Controllers/AdminEventController.php:289
* @route '/events/{event}/export'
*/
exportCsv.get = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportCsv.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminEventController::exportCsv
* @see app/Http/Controllers/AdminEventController.php:289
* @route '/events/{event}/export'
*/
exportCsv.head = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportCsv.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AdminEventController::exportCsv
* @see app/Http/Controllers/AdminEventController.php:289
* @route '/events/{event}/export'
*/
const exportCsvForm = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportCsv.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminEventController::exportCsv
* @see app/Http/Controllers/AdminEventController.php:289
* @route '/events/{event}/export'
*/
exportCsvForm.get = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportCsv.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminEventController::exportCsv
* @see app/Http/Controllers/AdminEventController.php:289
* @route '/events/{event}/export'
*/
exportCsvForm.head = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportCsv.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

exportCsv.form = exportCsvForm

/**
* @see \App\Http\Controllers\AdminEventController::index
* @see app/Http/Controllers/AdminEventController.php:22
* @route '/events'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/events',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AdminEventController::index
* @see app/Http/Controllers/AdminEventController.php:22
* @route '/events'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminEventController::index
* @see app/Http/Controllers/AdminEventController.php:22
* @route '/events'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminEventController::index
* @see app/Http/Controllers/AdminEventController.php:22
* @route '/events'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AdminEventController::index
* @see app/Http/Controllers/AdminEventController.php:22
* @route '/events'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminEventController::index
* @see app/Http/Controllers/AdminEventController.php:22
* @route '/events'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminEventController::index
* @see app/Http/Controllers/AdminEventController.php:22
* @route '/events'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\AdminEventController::store
* @see app/Http/Controllers/AdminEventController.php:40
* @route '/events'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/events',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminEventController::store
* @see app/Http/Controllers/AdminEventController.php:40
* @route '/events'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminEventController::store
* @see app/Http/Controllers/AdminEventController.php:40
* @route '/events'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AdminEventController::store
* @see app/Http/Controllers/AdminEventController.php:40
* @route '/events'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AdminEventController::store
* @see app/Http/Controllers/AdminEventController.php:40
* @route '/events'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\AdminEventController::update
* @see app/Http/Controllers/AdminEventController.php:108
* @route '/events/{event}'
*/
export const update = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/events/{event}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\AdminEventController::update
* @see app/Http/Controllers/AdminEventController.php:108
* @route '/events/{event}'
*/
update.url = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { event: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { event: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            event: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
    }

    return update.definition.url
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminEventController::update
* @see app/Http/Controllers/AdminEventController.php:108
* @route '/events/{event}'
*/
update.put = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\AdminEventController::update
* @see app/Http/Controllers/AdminEventController.php:108
* @route '/events/{event}'
*/
const updateForm = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AdminEventController::update
* @see app/Http/Controllers/AdminEventController.php:108
* @route '/events/{event}'
*/
updateForm.put = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\AdminEventController::toggleStatus
* @see app/Http/Controllers/AdminEventController.php:168
* @route '/events/{event}/status'
*/
export const toggleStatus = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: toggleStatus.url(args, options),
    method: 'patch',
})

toggleStatus.definition = {
    methods: ["patch"],
    url: '/events/{event}/status',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\AdminEventController::toggleStatus
* @see app/Http/Controllers/AdminEventController.php:168
* @route '/events/{event}/status'
*/
toggleStatus.url = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { event: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { event: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            event: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
    }

    return toggleStatus.definition.url
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminEventController::toggleStatus
* @see app/Http/Controllers/AdminEventController.php:168
* @route '/events/{event}/status'
*/
toggleStatus.patch = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: toggleStatus.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\AdminEventController::toggleStatus
* @see app/Http/Controllers/AdminEventController.php:168
* @route '/events/{event}/status'
*/
const toggleStatusForm = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggleStatus.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AdminEventController::toggleStatus
* @see app/Http/Controllers/AdminEventController.php:168
* @route '/events/{event}/status'
*/
toggleStatusForm.patch = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggleStatus.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

toggleStatus.form = toggleStatusForm

/**
* @see \App\Http\Controllers\AdminEventController::destroy
* @see app/Http/Controllers/AdminEventController.php:184
* @route '/events/{event}'
*/
export const destroy = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/events/{event}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AdminEventController::destroy
* @see app/Http/Controllers/AdminEventController.php:184
* @route '/events/{event}'
*/
destroy.url = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { event: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { event: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            event: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
    }

    return destroy.definition.url
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminEventController::destroy
* @see app/Http/Controllers/AdminEventController.php:184
* @route '/events/{event}'
*/
destroy.delete = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\AdminEventController::destroy
* @see app/Http/Controllers/AdminEventController.php:184
* @route '/events/{event}'
*/
const destroyForm = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AdminEventController::destroy
* @see app/Http/Controllers/AdminEventController.php:184
* @route '/events/{event}'
*/
destroyForm.delete = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const AdminEventController = { attendees, destroyAttendee, manualCheckin, resendTicket, exportCsv, index, store, update, toggleStatus, destroy }

export default AdminEventController
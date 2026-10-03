import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AdminEventController::destroy
* @see app/Http/Controllers/AdminEventController.php:344
* @route '/events/{event}/attendees/{attendee}'
*/
export const destroy = (args: { event: number | { id: number }, attendee: number | { id: number } } | [event: number | { id: number }, attendee: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/events/{event}/attendees/{attendee}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AdminEventController::destroy
* @see app/Http/Controllers/AdminEventController.php:344
* @route '/events/{event}/attendees/{attendee}'
*/
destroy.url = (args: { event: number | { id: number }, attendee: number | { id: number } } | [event: number | { id: number }, attendee: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{event}', parsedArgs.event.toString())
            .replace('{attendee}', parsedArgs.attendee.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminEventController::destroy
* @see app/Http/Controllers/AdminEventController.php:344
* @route '/events/{event}/attendees/{attendee}'
*/
destroy.delete = (args: { event: number | { id: number }, attendee: number | { id: number } } | [event: number | { id: number }, attendee: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\AdminEventController::destroy
* @see app/Http/Controllers/AdminEventController.php:344
* @route '/events/{event}/attendees/{attendee}'
*/
const destroyForm = (args: { event: number | { id: number }, attendee: number | { id: number } } | [event: number | { id: number }, attendee: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see app/Http/Controllers/AdminEventController.php:344
* @route '/events/{event}/attendees/{attendee}'
*/
destroyForm.delete = (args: { event: number | { id: number }, attendee: number | { id: number } } | [event: number | { id: number }, attendee: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

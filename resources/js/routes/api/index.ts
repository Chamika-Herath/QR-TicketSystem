import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\CheckInController::checkin
* @see app/Http/Controllers/CheckInController.php:25
* @route '/api/checkin/{token}'
*/
export const checkin = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: checkin.url(args, options),
    method: 'post',
})

checkin.definition = {
    methods: ["post"],
    url: '/api/checkin/{token}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CheckInController::checkin
* @see app/Http/Controllers/CheckInController.php:25
* @route '/api/checkin/{token}'
*/
checkin.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { token: args }
    }

    if (Array.isArray(args)) {
        args = {
            token: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        token: args.token,
    }

    return checkin.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CheckInController::checkin
* @see app/Http/Controllers/CheckInController.php:25
* @route '/api/checkin/{token}'
*/
checkin.post = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: checkin.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CheckInController::checkin
* @see app/Http/Controllers/CheckInController.php:25
* @route '/api/checkin/{token}'
*/
const checkinForm = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: checkin.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CheckInController::checkin
* @see app/Http/Controllers/CheckInController.php:25
* @route '/api/checkin/{token}'
*/
checkinForm.post = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: checkin.url(args, options),
    method: 'post',
})

checkin.form = checkinForm

/**
* @see routes/web.php:64
* @route '/api/events'
*/
export const events = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: events.url(options),
    method: 'get',
})

events.definition = {
    methods: ["get","head"],
    url: '/api/events',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:64
* @route '/api/events'
*/
events.url = (options?: RouteQueryOptions) => {
    return events.definition.url + queryParams(options)
}

/**
* @see routes/web.php:64
* @route '/api/events'
*/
events.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: events.url(options),
    method: 'get',
})

/**
* @see routes/web.php:64
* @route '/api/events'
*/
events.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: events.url(options),
    method: 'head',
})

/**
* @see routes/web.php:64
* @route '/api/events'
*/
const eventsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: events.url(options),
    method: 'get',
})

/**
* @see routes/web.php:64
* @route '/api/events'
*/
eventsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: events.url(options),
    method: 'get',
})

/**
* @see routes/web.php:64
* @route '/api/events'
*/
eventsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: events.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

events.form = eventsForm

const api = {
    events: Object.assign(events, events),
    checkin: Object.assign(checkin, checkin),
}

export default api
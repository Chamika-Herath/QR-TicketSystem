import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import events735790 from './events'
import users48860f from './users'
/**
* @see \App\Http\Controllers\AdminEventController::events
* @see app/Http/Controllers/AdminEventController.php:22
* @route '/events'
*/
export const events = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: events.url(options),
    method: 'get',
})

events.definition = {
    methods: ["get","head"],
    url: '/events',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AdminEventController::events
* @see app/Http/Controllers/AdminEventController.php:22
* @route '/events'
*/
events.url = (options?: RouteQueryOptions) => {
    return events.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminEventController::events
* @see app/Http/Controllers/AdminEventController.php:22
* @route '/events'
*/
events.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: events.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminEventController::events
* @see app/Http/Controllers/AdminEventController.php:22
* @route '/events'
*/
events.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: events.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AdminEventController::events
* @see app/Http/Controllers/AdminEventController.php:22
* @route '/events'
*/
const eventsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: events.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminEventController::events
* @see app/Http/Controllers/AdminEventController.php:22
* @route '/events'
*/
eventsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: events.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminEventController::events
* @see app/Http/Controllers/AdminEventController.php:22
* @route '/events'
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

/**
* @see \App\Http\Controllers\UserController::users
* @see app/Http/Controllers/UserController.php:17
* @route '/users'
*/
export const users = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: users.url(options),
    method: 'get',
})

users.definition = {
    methods: ["get","head"],
    url: '/users',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserController::users
* @see app/Http/Controllers/UserController.php:17
* @route '/users'
*/
users.url = (options?: RouteQueryOptions) => {
    return users.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::users
* @see app/Http/Controllers/UserController.php:17
* @route '/users'
*/
users.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: users.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::users
* @see app/Http/Controllers/UserController.php:17
* @route '/users'
*/
users.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: users.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserController::users
* @see app/Http/Controllers/UserController.php:17
* @route '/users'
*/
const usersForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: users.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::users
* @see app/Http/Controllers/UserController.php:17
* @route '/users'
*/
usersForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: users.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserController::users
* @see app/Http/Controllers/UserController.php:17
* @route '/users'
*/
usersForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: users.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

users.form = usersForm

const admin = {
    events: Object.assign(events, events735790),
    users: Object.assign(users, users48860f),
}

export default admin
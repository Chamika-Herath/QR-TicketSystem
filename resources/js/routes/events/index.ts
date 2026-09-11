import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\PublicEventController::show
* @see app/Http/Controllers/PublicEventController.php:19
* @route '/events/{slug}'
*/
export const show = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/events/{slug}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicEventController::show
* @see app/Http/Controllers/PublicEventController.php:19
* @route '/events/{slug}'
*/
show.url = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { slug: args }
    }

    if (Array.isArray(args)) {
        args = {
            slug: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        slug: args.slug,
    }

    return show.definition.url
            .replace('{slug}', parsedArgs.slug.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicEventController::show
* @see app/Http/Controllers/PublicEventController.php:19
* @route '/events/{slug}'
*/
show.get = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PublicEventController::show
* @see app/Http/Controllers/PublicEventController.php:19
* @route '/events/{slug}'
*/
show.head = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PublicEventController::show
* @see app/Http/Controllers/PublicEventController.php:19
* @route '/events/{slug}'
*/
const showForm = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PublicEventController::show
* @see app/Http/Controllers/PublicEventController.php:19
* @route '/events/{slug}'
*/
showForm.get = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PublicEventController::show
* @see app/Http/Controllers/PublicEventController.php:19
* @route '/events/{slug}'
*/
showForm.head = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\PublicEventController::register
* @see app/Http/Controllers/PublicEventController.php:33
* @route '/events/{slug}/register'
*/
export const register = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: register.url(args, options),
    method: 'post',
})

register.definition = {
    methods: ["post"],
    url: '/events/{slug}/register',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PublicEventController::register
* @see app/Http/Controllers/PublicEventController.php:33
* @route '/events/{slug}/register'
*/
register.url = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { slug: args }
    }

    if (Array.isArray(args)) {
        args = {
            slug: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        slug: args.slug,
    }

    return register.definition.url
            .replace('{slug}', parsedArgs.slug.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicEventController::register
* @see app/Http/Controllers/PublicEventController.php:33
* @route '/events/{slug}/register'
*/
register.post = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: register.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PublicEventController::register
* @see app/Http/Controllers/PublicEventController.php:33
* @route '/events/{slug}/register'
*/
const registerForm = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: register.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PublicEventController::register
* @see app/Http/Controllers/PublicEventController.php:33
* @route '/events/{slug}/register'
*/
registerForm.post = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: register.url(args, options),
    method: 'post',
})

register.form = registerForm

const events = {
    show: Object.assign(show, show),
    register: Object.assign(register, register),
}

export default events
import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\PublicEventController::show
* @see app/Http/Controllers/PublicEventController.php:104
* @route '/tickets/resend'
*/
export const show = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/tickets/resend',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicEventController::show
* @see app/Http/Controllers/PublicEventController.php:104
* @route '/tickets/resend'
*/
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicEventController::show
* @see app/Http/Controllers/PublicEventController.php:104
* @route '/tickets/resend'
*/
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PublicEventController::show
* @see app/Http/Controllers/PublicEventController.php:104
* @route '/tickets/resend'
*/
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PublicEventController::show
* @see app/Http/Controllers/PublicEventController.php:104
* @route '/tickets/resend'
*/
const showForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PublicEventController::show
* @see app/Http/Controllers/PublicEventController.php:104
* @route '/tickets/resend'
*/
showForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PublicEventController::show
* @see app/Http/Controllers/PublicEventController.php:104
* @route '/tickets/resend'
*/
showForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

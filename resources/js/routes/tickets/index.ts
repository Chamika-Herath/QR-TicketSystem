import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\PublicEventController::resend
* @see app/Http/Controllers/PublicEventController.php:120
* @route '/tickets/resend'
*/
export const resend = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resend.url(options),
    method: 'post',
})

resend.definition = {
    methods: ["post"],
    url: '/tickets/resend',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PublicEventController::resend
* @see app/Http/Controllers/PublicEventController.php:120
* @route '/tickets/resend'
*/
resend.url = (options?: RouteQueryOptions) => {
    return resend.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicEventController::resend
* @see app/Http/Controllers/PublicEventController.php:120
* @route '/tickets/resend'
*/
resend.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resend.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PublicEventController::resend
* @see app/Http/Controllers/PublicEventController.php:120
* @route '/tickets/resend'
*/
const resendForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resend.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PublicEventController::resend
* @see app/Http/Controllers/PublicEventController.php:120
* @route '/tickets/resend'
*/
resendForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resend.url(options),
    method: 'post',
})

resend.form = resendForm

const tickets = {
    resend: Object.assign(resend, resend),
}

export default tickets
import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\CheckInController::showScanner
* @see app/Http/Controllers/CheckInController.php:14
* @route '/scanner'
*/
export const showScanner = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showScanner.url(options),
    method: 'get',
})

showScanner.definition = {
    methods: ["get","head"],
    url: '/scanner',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CheckInController::showScanner
* @see app/Http/Controllers/CheckInController.php:14
* @route '/scanner'
*/
showScanner.url = (options?: RouteQueryOptions) => {
    return showScanner.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CheckInController::showScanner
* @see app/Http/Controllers/CheckInController.php:14
* @route '/scanner'
*/
showScanner.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showScanner.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CheckInController::showScanner
* @see app/Http/Controllers/CheckInController.php:14
* @route '/scanner'
*/
showScanner.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showScanner.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CheckInController::showScanner
* @see app/Http/Controllers/CheckInController.php:14
* @route '/scanner'
*/
const showScannerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showScanner.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CheckInController::showScanner
* @see app/Http/Controllers/CheckInController.php:14
* @route '/scanner'
*/
showScannerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showScanner.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CheckInController::showScanner
* @see app/Http/Controllers/CheckInController.php:14
* @route '/scanner'
*/
showScannerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showScanner.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

showScanner.form = showScannerForm

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

const CheckInController = { showScanner, checkin }

export default CheckInController
import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\IncomeController::index
* @see app/Http/Controllers/IncomeController.php:12
* @route '/income'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/income',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\IncomeController::index
* @see app/Http/Controllers/IncomeController.php:12
* @route '/income'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\IncomeController::index
* @see app/Http/Controllers/IncomeController.php:12
* @route '/income'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\IncomeController::index
* @see app/Http/Controllers/IncomeController.php:12
* @route '/income'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\IncomeController::index
* @see app/Http/Controllers/IncomeController.php:12
* @route '/income'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\IncomeController::index
* @see app/Http/Controllers/IncomeController.php:12
* @route '/income'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\IncomeController::index
* @see app/Http/Controllers/IncomeController.php:12
* @route '/income'
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

const IncomeController = { index }

export default IncomeController
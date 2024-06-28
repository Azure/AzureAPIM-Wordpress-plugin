import {useCallback, useState} from "react"
import useLoad, {loadData} from "./useLoad"
import {getUserId} from "./userHandlers"
import {SubscriptionState, TPaginated, TProduct, TSubscription, TUserInfo} from "./types"
import {getBsonObjectId} from "../utils"

const scopeToUser = () => {
    const userId = getUserId()
    return userId ? `/users/${userId}` : ""
}

export const useLoadApis = () =>
    useLoad(`${scopeToUser()}/apis`, "apisList")
export const useLoadApiDetail = (id: string) =>
    useLoad(`${scopeToUser()}/apis/${id}`, "apiDetail")

const apiDefinitionHeaders = {Accept: "application/vnd.swagger.doc+json"}
export const useLoadApiDefinition = (id: string) =>
    useLoad(`${scopeToUser()}/apis/${id}?export=true`, "apiDefinition", {headers: apiDefinitionHeaders})

export const useLoadProducts = () =>
    useLoad<TPaginated<TProduct>>(`${scopeToUser()}/products`, "productsList")
export const useLoadProductDetail = (id: string, ) =>
    useLoad(`${scopeToUser()}/products/${id}`, "productDetail")
export const useLoadProductApis = (id: string) =>
    useLoad(`${scopeToUser()}/products/${id}/apis`, "productApis")

export const useLoadUserInfo = () =>
    useLoad<TUserInfo>(`/users/${getUserId()}`, "userDetail")

const processSubscriptions = (data: TPaginated<TSubscription>) => {
    data.value = data.value.map(v => ({ ...v, state: SubscriptionState[v.state] }))
    return data
}
export const useLoadSubscriptions = () =>
    useLoad<TPaginated<TSubscription>>(`/users/${getUserId()}/subscriptions`, "userSubscriptions", {dataProcessor: processSubscriptions})
export const useLoadSubscriptionsScoped = (scope: string) =>
    useLoad<TPaginated<TSubscription>>(`/users/${getUserId()}/subscriptions?$filter=endswith(scope,%20%27${encodeURIComponent(scope)}%27)`, "userSubscriptionsScoped", {dataProcessor: processSubscriptions})

export const usePostSubscribe = () => {
    const [data, setData] = useState()
    const [loading, setLoading] = useState(false)

    const send = useCallback((body: Record<string, unknown>) => {
        const id = getBsonObjectId()
        const headers = { "Content-Type": "application/json" }
        setLoading(true)
        return loadData(`/users/${getUserId()}/subscriptions/${id}`, "createSubscription", headers, {method: "POST", body: JSON.stringify(body)})
            .then(setData)
            .finally(() => setLoading(false))
    }, [])

    return [send, {data, loading}]
}

export const usePatchSubscriptionCancel = () => {
    const [data, setData] = useState()
    const [loading, setLoading] = useState(false)

    const send = useCallback((id: string) => {
        const headers = { "Content-Type": "application/json", "If-Match": "*" }
        setLoading(true)
        return loadData(`/users/${getUserId()}/subscriptions/${id}`, "cancelSubscription", headers, {method: "PATCH", body: JSON.stringify({"state":"Cancelled"})})
            .then(setData)
            .finally(() => setLoading(false))
    }, [])

    return [send, {data, loading}]
}

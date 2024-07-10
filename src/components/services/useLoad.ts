// Copyright (c) Microsoft Corporation.
// Licensed under the MIT License.

import {useCallback, useEffect, useState} from "react"
import {serviceName} from "../../constants"
import {getSasToken} from "./userHandlers"
import {getPortalHeader} from "../utils"
import {handleError} from "./errorHandling"

const apiVersion = "2022-04-01-preview"

export const loadData = async (path: string, key: string, headersProp?: HeadersInit, requestInit?: RequestInit) => {
    const url = new URL(`https://${serviceName}.developer.azure-api.net/developer${path}`)
    url.searchParams.set("api-version", apiVersion)

    const headers = new Headers(headersProp)
    headers.append("x-ms-apim-client", getPortalHeader(key)) // telemetry

    const sasToken = getSasToken()
    if (sasToken) {
        headers.append("Authorization", "SharedAccessSignature " + sasToken)
    }

    return fetch(url, {...requestInit, headers})
        .then(async e => e.ok ? e.json() : handleError(await e.json()))
}

type OptionalObj<T> = {
    headers?: HeadersInit;
    requestInit?: RequestInit;
    dataProcessor?: (data: T) => T;
}

const useLoad = <T>(
    path: string,
    key: string,
    {
        headers,
        requestInit,
        dataProcessor = (d: T): T => d,
    }: OptionalObj<T> = {}
): [T, { reload: () => void }] => {
    const [data, setData] = useState<T>()
    const [refresher, setRefresher] = useState(0)

    useEffect(() => {
        loadData(path, key, headers, requestInit).then(values => setData(dataProcessor(values)))
    }, [path, key, headers, requestInit, refresher])

    const reload = useCallback(() => setRefresher(old => old + 1), [])

    return [data, {reload}]
}

export default useLoad

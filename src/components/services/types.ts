// Copyright (c) Microsoft Corporation.
// Licensed under the MIT License.

export type TPaginated<T> = {
    nextLink: string | null,
    value: T[]
}

export type TUserInfo = {
    email: string,
    firstName: string,
    id: string,
    lastName: string,
    registrationDate: string,
    state: string,
}

export enum SubscriptionState {
    suspended = "Suspended",
    active = "Active",
    expired = "Expired",
    submitted = "Submitted",
    rejected = "Rejected",
    cancelled = "Cancelled"
}

export type TSubscription = {
    createdDate: string
    endDate: string | null
    expirationDate: string | null
    id: string
    name: string
    notificationDate: string | null
    ownerId: string
    primaryKey: string
    scope: string
    secondaryKey: string
    startDate: string | null
    state: SubscriptionState
    stateComment: null
}

export type TProduct = {
    id: string
    name: string
    description: string
    terms: string
    approvalRequired: boolean
    subscriptionRequired: boolean
    subscriptionsLimit: number
}
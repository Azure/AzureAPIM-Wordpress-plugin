import {SubscriptionState, TSubscription} from "./services/types"

export const renderDate = (dateString: string) => dateString && new Date(dateString).toLocaleDateString()

export const isSubscriptionActive = (subscription: TSubscription) => subscription.state === SubscriptionState.active

export const getBsonObjectId = (): string => {
    const timestamp = (new Date().getTime() / 1000 | 0).toString(16);

    return timestamp + "xxxxxxxxxxxxxxxx".replace(/[x]/g, () => {
        return (Math.random() * 16 | 0).toString(16);
    }).toLowerCase();
}

export const getPortalHeader = (eventName?: string) => {
    return `wordpress|${window.location.host}|${eventName ?? ""}`
}

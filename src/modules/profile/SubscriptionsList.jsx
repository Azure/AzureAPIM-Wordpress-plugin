import {useState} from "react"
import {useLoadSubscriptions, usePatchSubscriptionCancel} from "../../components/services"
import {isSubscriptionActive, renderDate} from "../../components/utils"

const SubscriptionsList = () => {
    const [subscriptions, {reload}] = useLoadSubscriptions()
    const [cancelSubscription, {loading}] = usePatchSubscriptionCancel()

    const [showPrimary, setShowPrimary] = useState(false)
    const [showSecondary, setShowSecondary] = useState(false)

    return (
        <>
            <h4>Subscriptions</h4>
            {!subscriptions ? (
                <div>Loading subscriptions</div>
            ) : !subscriptions.value || !subscriptions.value.length ? (
                <div>You don't have any subscriptions yet</div>
            ) : (
                <table className={"apim-table"}>
                    <thead>
                    <tr>
                        <th>Subscription details</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    {subscriptions && subscriptions.value && subscriptions.value.map(sub => (
                        <tr key={sub.id}>
                            <td>
                                {!isSubscriptionActive(sub) ? (
                                    <b>{sub.name}</b>
                                ) : (
                                    <div className={"apim-grid"}>
                                        <div>Name</div>
                                        <b>{sub.name}</b>
                                        <div>Started on</div>
                                        <b>{renderDate(sub.createdDate)}</b>
                                        <div>Primary key</div>
                                        {showPrimary ? (
                                            <b>{sub.primaryKey}</b>
                                        ) : (
                                            <button className={"apim-link"} onClick={() => setShowPrimary(true)} type="button">
                                                Show
                                            </button>
                                        )}
                                        <div>Secondary key</div>
                                        {showSecondary ? (
                                            <b>{sub.secondaryKey}</b>
                                        ) : (
                                            <button className={"apim-link"} onClick={() => setShowSecondary(true)} type="button">
                                                Show
                                            </button>
                                        )}
                                    </div>
                                )}
                            </td>
                            <td>{sub.state}</td>
                            <td>
                                {isSubscriptionActive(sub) && (
                                    <button
                                        className={"apim-link"}
                                        onClick={() => cancelSubscription(sub.id).finally(reload)}
                                        disabled={loading}
                                        type="button"
                                    >
                                        Cancel
                                    </button>
                                )}
                            </td>
                        </tr>
                    ))}
                    </tbody>
                </table>
            )}
        </>
    )
}

export default SubscriptionsList

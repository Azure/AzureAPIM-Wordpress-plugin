import {useRef} from "react"
import ReactMarkdown from "react-markdown"

import {
    useLoadProductApis,
    useLoadProductDetail,
    useLoadSubscriptionsScoped,
    usePostSubscribe,
} from "../../components/services"
import {ApisListView} from "../apisList/ApisList"
import {isUserLoggedIn} from "../../components/services/userHandlers"
import {SubscriptionState} from "../../components/services/types"

const SubscriptionNew = ({productId, reload, limitReached}) => {
    const [postSubscribe, {loading}] = usePostSubscribe()
    const nameRef = useRef(null)

    return (
        <>
            <h4>New subscription</h4>

            {limitReached ? (
                <p>You've reached maximum number of subscriptions.</p>
            ) : (
                <form onSubmit={e => {
                    e.preventDefault()
                    postSubscribe({name: nameRef.current.value, scope: `/products/${productId}`})
                        .then(() => nameRef.current.value = "")
                        .finally(reload)
                }}>
                    <label className={"apim-label"}>
                        Name: <input ref={nameRef} type={"text"} className={"apim-input"} />
                    </label>
                    <input type={"submit"} className={"apim-button"} disabled={loading} />
                </form>
            )}
        </>
    )
}

const SubscriptionsList = ({product}) => {
    const [subscriptions, {reload}] = useLoadSubscriptionsScoped(product.name)

    const activeSubscriptions = (subscriptions?.value?.filter(item => item.state === SubscriptionState.active || item.state === SubscriptionState.submitted)) || []
    const limitReached = (product.subscriptionsLimit || product.subscriptionsLimit === 0) && product.subscriptionsLimit <= activeSubscriptions.length

    return (
        <>
            <h4>Subscriptions list</h4>

            {!subscriptions ? (
                <div>Loading subscriptions</div>
            ) : (
                <>
                    {!subscriptions.value || !subscriptions.value.length ? (
                        <div>You don't have any subscriptions yet</div>
                    ) : (
                        <table className={"apim-table"}>
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            {subscriptions && subscriptions.value && subscriptions.value.map(sub => (
                                <tr key={sub.id}>
                                    <td>{sub.name}</td>
                                    <td>{sub.state}</td>
                                </tr>
                            ))}
                            </tbody>
                        </table>
                    )}

                    <SubscriptionNew productId={product.id} reload={reload} limitReached={limitReached} />
                </>
            )}
        </>
    )
}

const ProductDetail = ({id}) => {
    const [product] = useLoadProductDetail(id)
    const [productApis] = useLoadProductApis(id)
    const isUser = isUserLoggedIn()

    return (
        <div>
            {product ? (
                <>
                    <h3>{product.name}</h3>

                    <ReactMarkdown>{product.description}</ReactMarkdown>

                    {isUser && (
                        <div style={{marginBottom: "1em"}}>
                            <SubscriptionsList product={product} />
                        </div>
                    )}

                    {productApis ? (
                        <>
                            <h4>APIs List</h4>
                            <ApisListView apis={productApis} />
                        </>
                    ) : <div>Loading APIs</div>}
                </>
            ) : (
                <div>
                    Loading <b>{id}</b>
                </div>
            )}

        </div>
    )
}

export default ProductDetail

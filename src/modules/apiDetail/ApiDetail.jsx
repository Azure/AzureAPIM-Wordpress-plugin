import SwaggerUI from "swagger-ui-react"
import "./styles.module.scss"

import {useLoadApiDefinition, useLoadApiDetail} from "../../components/services"

const ApiDetail = ({id}) => {
    const [api] = useLoadApiDetail(id)
    const [def] = useLoadApiDefinition(id)

    if (!api || !def) {
        return (
            <div>
                Loading <b>{id}</b>
            </div>
        )
    }

    return <SwaggerUI spec={def} />
}

export default ApiDetail

import {useLoadApis} from "../../components/services"
import c from "./styles.module.scss"

export const ApisListView = ({apis}) => (
    <table className={"apim-table"}>
        <thead>
        <tr>
            <th style={{width: "25%"}}>Name</th>
            <th style={{width: "25%"}}>Type</th>
            <th>Description</th>
        </tr>
        </thead>
        <tbody>
        {apis && apis.value && apis.value.map(api => (
            <tr key={api.id}>
                <td>
                    <a href={`/api-details/?id=${api.id}`}>{api.id}</a>
                </td>
                <td>{api.protocols.join(", ").toUpperCase()}</td>
                <td className={c.ellipseRow} title={api.description}>
                    {api.description}
                </td>
            </tr>
        ))}
        </tbody>
    </table>
)

const ApisList = () => {
    const [apis] = useLoadApis()

    if (!apis) return <p>Loading</p>

    return <ApisListView apis={apis} />
}

export default ApisList

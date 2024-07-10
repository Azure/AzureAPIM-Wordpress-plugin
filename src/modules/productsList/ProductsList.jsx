// Copyright (c) Microsoft Corporation.
// Licensed under the MIT License.

import {useLoadProducts} from "../../components/services"
import c from "./styles.module.scss"

const ProductsList = () => {
    const [products] = useLoadProducts()

    if (!products) return <p>Loading</p>

    return (
        <table className={"apim-table"}>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                {products && products.value && products.value.map(product => (
                    <tr key={product.id}>
                        <td>
                            <a href={`/product-details/?id=${product.id}`}>{product.id}</a>
                        </td>
                        <td className={c.ellipseRow} title={product.description}>
                            {product.description}
                        </td>
                    </tr>
                ))}
            </tbody>
        </table>
    )
}

export default ProductsList

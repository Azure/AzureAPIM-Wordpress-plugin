import React from "react"
import ProductDetail from "./ProductDetail"

const App = () => {
    const id = new URLSearchParams(window.location.search).get("id")

    if (!id) return <div>ID is missing in the search params</div>

    return <ProductDetail id={id} />
}

export default App

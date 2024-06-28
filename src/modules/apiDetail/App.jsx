import React from "react"
import ApiDetail from "./ApiDetail"

const App = () => {
    const id = new URLSearchParams(window.location.search).get("id")

    if (!id) return <div>ID is missing in the search params</div>

    return <ApiDetail id={id} />
}

export default App

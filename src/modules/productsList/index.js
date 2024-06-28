import {render} from "@wordpress/element"
import App from "./App"

const element = document.getElementById("apim-products-list")
if (element) render(<App />, element)

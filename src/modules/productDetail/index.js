import {render} from "@wordpress/element"
import App from "./App"

const element = document.getElementById("apim-product-details")
if (element) render(<App />, element)

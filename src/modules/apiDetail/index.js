import {render} from "@wordpress/element"
import App from "./App"

const element = document.getElementById("apim-api-details")
if (element) render(<App />, element)

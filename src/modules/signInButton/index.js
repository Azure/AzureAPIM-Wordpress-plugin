import {render} from "@wordpress/element"
import App from "./App"

const element = document.getElementById("apim-signIn")
if (element) render(<App />, element)

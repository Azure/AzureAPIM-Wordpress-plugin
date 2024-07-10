// Copyright (c) Microsoft Corporation.
// Licensed under the MIT License.

const displayError = alert // TODO

export function handleError(e) {
    console.error(e)

    let errorMessage

    if (e?.message) {
        try {
            const parsed = JSON.parse(e.message)
            errorMessage = parsed?.error?.message
        } catch (e) {
            errorMessage = e.message
        }
    }

    if (!errorMessage && e?.response?.status) {
        switch (e.response.status) {
            case 400:
                errorMessage = "Invalid request. Please check your input and try again."
                break
            case 401:
                errorMessage = "Unauthorized. Please log in and try again."
                break
            case 403:
                errorMessage = "Access denied. You do not have permission to perform this action."
                break
            case 404:
                errorMessage = "Product not found. Please check the product ID and try again."
                break
            case 500:
                errorMessage = "Server error. Please try again later."
                break
            default:
                errorMessage = `Unexpected error occurred: ${e.message}`
        }
    }

    displayError(errorMessage ?? "Unexpected error occurred")

    return e
}

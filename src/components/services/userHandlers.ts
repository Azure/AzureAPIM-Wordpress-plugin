const OcpSasToken = "Ocp-Apim-Sas-Token"
const OcpUserId = "Ocp-Apim-User-Id"

function getCookieValue(name: string): string {
    const match = document.cookie.match(new RegExp("(^| )" + name + "=([^;]+)"))
    if (match) return decodeURIComponent(match[2])
}

export function getSasToken() {
    return getCookieValue(OcpSasToken)
}

export function getUserId() {
    return getCookieValue(OcpUserId)
}

export function isUserLoggedIn(): boolean {
    return !!getSasToken()
}

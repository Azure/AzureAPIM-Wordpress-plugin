import {isUserLoggedIn} from "../../components/services/userHandlers"

const SignInButton = () => {
    const userLoggedIn = isUserLoggedIn()

    if (userLoggedIn) {
        return (
            <>
                <a href={"/profile"} className={"apim-nav-link"}>Profile</a>
                <a href={"/.auth/logout?post_logout_redirect_uri=/"} className={"apim-nav-link-btn"}>Sign<>&nbsp;</>out</a>
            </>
        )
    }

    return (
        <a href={"/.auth/login/aad?post_login_redirect_uri=/profile"} className={"apim-nav-link-btn"}>Sign<>&nbsp;</>In</a>
    )
}

export default SignInButton

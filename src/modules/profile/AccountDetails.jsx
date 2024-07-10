// Copyright (c) Microsoft Corporation.
// Licensed under the MIT License.

import {useLoadUserInfo} from "../../components/services"
import {renderDate} from "../../components/utils"

const AccountDetails = () => {
    const [userInfo] = useLoadUserInfo()

    return (
        <>
            <h4>Account details</h4>

            {!userInfo ? <div>Loading your personal details</div> : (
                <div className={"apim-grid"}>
                    <div>Email</div>
                    <b>{userInfo.email}</b>
                    <div>First name</div>
                    <b>{userInfo.firstName}</b>
                    <div>Last name</div>
                    <b>{userInfo.lastName}</b>
                    <div>Registration date</div>
                    <b>{renderDate(userInfo.registrationDate)}</b>
                </div>
            )}
            <br/>
            <a href={"/.auth/logout?post_logout_redirect_uri=/"} className={"apim-button"}>
                Sign out
            </a>
        </>
    )
}

export default AccountDetails
